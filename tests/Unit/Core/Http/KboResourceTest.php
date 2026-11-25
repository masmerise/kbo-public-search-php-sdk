<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Http;

use Exception;
use KboPublicSearch\Authentication\Credentials;
use KboPublicSearch\Core\Failure\RequestLimitReached;
use KboPublicSearch\Core\Failure\UnknownException;
use KboPublicSearch\Core\Http\KboConnector;
use KboPublicSearch\Core\Reply\ReplyStatus;
use KboPublicSearch\Environments\Environment;
use KboPublicSearch\Localization\Languages;
use KboPublicSearch\Tests\Support\CouldNotProbe;
use KboPublicSearch\Tests\Support\ProbeRequest;
use KboPublicSearch\Tests\Support\ProbeResource;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;

final class KboResourceTest extends TestCase
{
    private MockClient $mock;

    private ProbeResource $resource;

    #[Test]
    public function sends_soap_action_and_envelope(): void
    {
        // Arrange
        $this->respond(MockResponse::make(self::reply('00001'), 200));

        // Act
        $this->resource->probeLookup(new ProbeRequest('0314595348'));

        // Assert
        $pending = $this->mock->getLastPendingRequest();
        $body = (string) $pending?->body();
        $this->assertSame('http://fgov.economie.be/kbopub/ReadEnterprise', $pending?->headers()->get('SOAPAction'));
        $this->assertSame('text/xml; charset=utf-8', $pending?->headers()->get('Content-Type'));
        $this->assertSame('POST', $pending?->getMethod()->value);
        $this->assertSame(Environment::Test->url(), $pending?->getUrl());
        $this->assertStringContainsString('<wsse:UsernameToken>', $body);
        $this->assertStringContainsString('<mes:ReadEnterpriseRequest><dat:EnterpriseNumber>0314595348</dat:EnterpriseNumber></mes:ReadEnterpriseRequest>', $body);
        $this->assertStringContainsString('<mes:Language>fr</mes:Language>', $body);
        $this->assertStringNotContainsString('secret', $body);
    }

    #[Test]
    public function lookup_throws_on_canceled(): void
    {
        // Arrange
        $this->respond(MockResponse::make(file_get_contents(__DIR__ . '/../../../Fixtures/Cookbook/reply-enterprise-canceled.xml'), 200));

        // Act
        try {
            $this->resource->probeLookup(new ProbeRequest('0206231995'));
            $this->fail('Expected CouldNotProbe.');
        } catch (CouldNotProbe $ex) {
            // Assert
            $this->assertSame(ReplyStatus::EnterpriseCanceled, $ex->status);
            $this->assertSame(14, $ex->getCode());
            $this->assertSame('Enterprise canceled', $ex->getMessage());
            $this->assertSame('myid', $ex->context->requestId);
        }
    }

    #[Test]
    public function lookup_rejects_no_data(): void
    {
        // Arrange
        $this->respond(MockResponse::make(self::reply('00003'), 200));

        // Assert
        $this->expectException(CouldNotProbe::class);

        // Act
        $this->resource->probeLookup(new ProbeRequest('0314595348'));
    }

    #[Test]
    #[DataProvider('acceptedBySearch')]
    public function search_accepts_no_data_and_truncated(string $code): void
    {
        // Arrange
        $this->respond(MockResponse::make(self::reply($code), 200));

        // Act
        $result = $this->resource->probeSearch(new ProbeRequest('0314595348'));

        // Assert
        $this->assertSame($code, $result->context->statusCode);
    }

    public static function acceptedBySearch(): iterable
    {
        yield 'success' => ['00001'];
        yield 'uncharged success' => ['10001'];
        yield 'no data' => ['00003'];
        yield 'uncharged no data' => ['10003'];
        yield 'truncated' => ['00008'];
    }

    #[Test]
    #[DataProvider('rejectedBySearch')]
    public function search_rejects_other_statuses(string $code): void
    {
        // Arrange
        $this->respond(MockResponse::make(self::reply($code), 200));

        // Assert
        $this->expectException(CouldNotProbe::class);
        $this->expectExceptionCode((int) $code);

        // Act
        $this->resource->probeSearch(new ProbeRequest('0314595348'));
    }

    public static function rejectedBySearch(): iterable
    {
        yield 'invalid enterprise number' => ['00005'];
        yield 'juridical form' => ['00007'];
        yield 'unknown' => ['99999'];
    }

    #[Test]
    public function fault_with_http_500_is_typed(): void
    {
        // Arrange
        $this->respond(MockResponse::make(file_get_contents(__DIR__ . '/../../../Fixtures/Cookbook/fault-quota.xml'), 500));

        // Assert
        $this->expectException(RequestLimitReached::class);

        // Act
        $this->resource->probeLookup(new ProbeRequest('0314595348'));
    }

    #[Test]
    #[DataProvider('proxyErrors')]
    public function proxy_error_is_unknown(string $body, int $status): void
    {
        // Arrange
        $this->respond(MockResponse::make($body, $status));

        // Assert
        $this->expectException(UnknownException::class);

        // Act
        $this->resource->probeSearch(new ProbeRequest('0314595348'));
    }

    public static function proxyErrors(): iterable
    {
        yield '502 html' => ['<html><body><h1>502 Bad Gateway</h1></body></html>', 502];
        yield '502 broken html' => ['<html><body>502 Bad Gateway', 502];
        yield '503 empty' => ['', 503];
        yield '200 json' => ['{"error":"nope"}', 200];
    }

    #[Test]
    public function mapping_failure_is_unknown(): void
    {
        // Arrange
        $this->respond(MockResponse::make(self::reply('00001'), 200));

        // Assert
        $this->expectException(UnknownException::class);
        $this->expectExceptionMessage('Element [Missing] not found.');

        // Act
        $this->resource->probeMapping(new ProbeRequest('0314595348'));
    }

    #[Test]
    public function unknown_reply_names_the_http_status_and_body(): void
    {
        // Arrange
        $this->respond(MockResponse::make("<html>\n<body><h1>502 Bad Gateway</h1></body></html>", 502));

        // Act
        try {
            $this->resource->probeSearch(new ProbeRequest('0314595348'));
            $this->fail('Expected UnknownException.');
        } catch (UnknownException $ex) {
            // Assert
            $this->assertStringContainsString('HTTP 502', $ex->getMessage());
            $this->assertStringContainsString('<html> <body><h1>502 Bad Gateway</h1>', $ex->getMessage());
        }
    }

    #[Test]
    public function unknown_reply_excerpt_is_short(): void
    {
        // Arrange
        $this->respond(MockResponse::make(str_repeat('x', 5000), 503));

        // Act
        try {
            $this->resource->probeSearch(new ProbeRequest('0314595348'));
            $this->fail('Expected UnknownException.');
        } catch (UnknownException $ex) {
            // Assert
            $this->assertStringContainsString('HTTP 503', $ex->getMessage());
            $this->assertLessThan(400, strlen($ex->getMessage()));
        }
    }

    #[Test]
    public function connection_failure_is_unknown(): void
    {
        // Arrange
        $this->respond(MockResponse::make()->throw(fn (PendingRequest $pending) => new FatalRequestException(new Exception('Could not resolve host'), $pending)));

        // Assert
        $this->expectException(UnknownException::class);
        $this->expectExceptionMessage('Could not resolve host');

        // Act
        $this->resource->probeLookup(new ProbeRequest('0314595348'));
    }

    private function respond(MockResponse $response): void
    {
        $this->mock = new MockClient([ProbeRequest::class => $response]);

        $connector = new KboConnector(Credentials::present('userid', 'secret'));
        $connector->useEnvironment(Environment::Test);
        $connector->useLanguages(Languages::french());
        $connector->withMockClient($this->mock);

        $this->resource = new ProbeResource($connector);
    }

    private static function reply(string $code): string
    {
        return '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Header>'
            . '<ReplyContext xmlns="http://economie.fgov.be/kbopub/webservices/v1/messages"><RequestId>id</RequestId><ReplyId>rid</ReplyId>'
            . "<ReplyStatus><Code>{$code}</Code><Description>Status {$code}</Description></ReplyStatus><AccountBalance>2999</AccountBalance></ReplyContext>"
            . '</soap:Header><soap:Body><ReadEnterpriseReply xmlns="http://economie.fgov.be/kbopub/webservices/v1/messages"/></soap:Body></soap:Envelope>';
    }
}
