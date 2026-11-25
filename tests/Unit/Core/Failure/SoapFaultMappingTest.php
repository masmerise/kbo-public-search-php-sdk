<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Failure;

use KboPublicSearch\Authentication\Failure\CouldNotAuthenticate;
use KboPublicSearch\Authentication\Failure\MustAcceptTermsOfService;
use KboPublicSearch\Core\Failure\CouldNotCompleteRequest;
use KboPublicSearch\Core\Failure\KboPublicSearchException;
use KboPublicSearch\Core\Failure\RequestLimitReached;
use KboPublicSearch\Core\Http\Soap\Envelope;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class SoapFaultMappingTest extends TestCase
{
    /** @param class-string<KboPublicSearchException> $expected */
    #[Test]
    #[DataProvider('faults')]
    public function maps_faults_to_typed_exceptions(string $xml, string $expected): void
    {
        // Act
        $ex = $this->unwrap($xml);

        // Assert
        $this->assertSame($expected, $ex::class);
    }

    public static function faults(): iterable
    {
        yield 'terms' => [self::fixture('fault-terms'), MustAcceptTermsOfService::class];
        yield 'authentication' => [self::fixture('fault-authentication'), CouldNotAuthenticate::class];
        yield 'wss4j security error (wrong credentials)' => [self::fixture('fault-security-error'), CouldNotAuthenticate::class];
        yield 'quota' => [self::fixture('fault-quota'), RequestLimitReached::class];
        yield 'balance' => [self::fixture('fault-balance'), RequestLimitReached::class];
        yield 'unmarshalling' => [self::fixture('fault-unmarshalling'), CouldNotCompleteRequest::class];
        yield 'unknown wording' => [self::fault('soap:Server', 'Something new'), CouldNotCompleteRequest::class];
        yield 'case-insensitive quota' => [self::fault('soap:Client', 'QUOTA EXCEEDED'), RequestLimitReached::class];
    }

    #[Test]
    public function terms_fault_exposes_the_url_and_a_single_line_message(): void
    {
        // Act
        $ex = $this->unwrap(self::fixture('fault-terms'));

        // Assert
        $this->assertInstanceOf(MustAcceptTermsOfService::class, $ex);
        $this->assertSame('https://kbopub.economie.fgov.be/kbo-open-data/login', $ex->url);
        $this->assertSame('You must go to https://kbopub.economie.fgov.be/kbo-open-data/login and accept the terms of service!', $ex->getMessage());
    }

    #[Test]
    public function unmarshalling_fault_exposes_code_and_string(): void
    {
        // Act
        $ex = $this->unwrap(self::fixture('fault-unmarshalling'));

        // Assert
        $this->assertInstanceOf(CouldNotCompleteRequest::class, $ex);
        $this->assertSame('soap:Server', $ex->faultCode);
        $this->assertStringStartsWith('Unmarshalling Error: cvc-complex-type.2.4.a', $ex->faultString);
    }

    private function unwrap(string $xml): KboPublicSearchException
    {
        try {
            Envelope::unwrap($xml);
        } catch (KboPublicSearchException $ex) {
            return $ex;
        }

        $this->fail('No exception was thrown.');
    }

    private static function fixture(string $name): string
    {
        return file_get_contents(__DIR__ . "/../../../Fixtures/Cookbook/{$name}.xml");
    }

    private static function fault(string $code, string $string): string
    {
        return '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body><soap:Fault>'
            . "<faultcode>{$code}</faultcode><faultstring>{$string}</faultstring></soap:Fault></soap:Body></soap:Envelope>";
    }
}
