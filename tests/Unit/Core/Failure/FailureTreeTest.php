<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Failure;

use Exception;
use KboPublicSearch\Authentication\Failure\CouldNotAuthenticate;
use KboPublicSearch\Authentication\Failure\MustAcceptTermsOfService;
use KboPublicSearch\Core\Failure\CouldNotCompleteRequest;
use KboPublicSearch\Core\Failure\KboPublicSearchException;
use KboPublicSearch\Core\Failure\RequestLimitReached;
use KboPublicSearch\Core\Failure\UnknownException;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class FailureTreeTest extends TestCase
{
    #[Test]
    public function must_accept_terms_extracts_url(): void
    {
        // Act
        $ex = MustAcceptTermsOfService::because('You must go to https://kbopub.economie.fgov.be/kbo-open-data/login and accept the terms of service!');

        // Assert
        $this->assertSame('https://kbopub.economie.fgov.be/kbo-open-data/login', $ex->url);
        $this->assertInstanceOf(CouldNotAuthenticate::class, $ex);
        $this->assertInstanceOf(KboPublicSearchException::class, $ex);
    }

    #[Test]
    public function must_accept_terms_without_url(): void
    {
        $this->assertNull(MustAcceptTermsOfService::because('accept the terms of service')->url);
    }

    #[Test]
    public function unknown_wraps_previous(): void
    {
        // Arrange
        $e = new Exception('boom', 7);

        // Act
        $ex = UnknownException::sorry($e);

        // Assert
        $this->assertSame($e, $ex->getPrevious());
        $this->assertSame('boom', $ex->getMessage());
        $this->assertSame(7, $ex->getCode());
    }

    #[Test]
    public function could_not_complete_request_exposes_the_fault(): void
    {
        // Act
        $ex = new CouldNotCompleteRequest('soap:Server', 'Unmarshalling Error');

        // Assert
        $this->assertSame('soap:Server', $ex->faultCode);
        $this->assertSame('Unmarshalling Error', $ex->faultString);
        $this->assertSame('Unmarshalling Error', $ex->getMessage());
    }

    #[Test]
    public function request_limit_and_authentication_carry_the_fault_string(): void
    {
        $this->assertSame('Quota Exceeded!', RequestLimitReached::because('Quota Exceeded!')->getMessage());
        $this->assertSame('nope', CouldNotAuthenticate::because('nope')->getMessage());
    }
}
