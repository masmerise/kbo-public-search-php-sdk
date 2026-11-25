<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Http\Soap;

use KboPublicSearch\Core\Failure\UnknownException;
use KboPublicSearch\Core\Http\Soap\Envelope;
use KboPublicSearch\Core\Reply\ReplyStatus;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class EnvelopeUnwrapTest extends TestCase
{
    #[Test]
    public function canceled_reply(): void
    {
        // Act
        $result = Envelope::unwrap(file_get_contents(__DIR__ . '/../../../../Fixtures/Cookbook/reply-enterprise-canceled.xml'));

        // Assert
        $this->assertSame(ReplyStatus::EnterpriseCanceled, $result->context->status);
        $this->assertSame('00014', $result->context->statusCode);
        $this->assertSame('Enterprise canceled', $result->context->statusDescription);
        $this->assertSame('myid', $result->context->requestId);
        $this->assertSame('23efbf0d-7d1e-4f67-981e-e1e2a766a5e0', $result->context->replyId);
        $this->assertNull($result->context->accountBalance);
        $this->assertNull($result->context->snapshotDate);
        $this->assertTrue($result->context->charged());
        $this->assertSame('ReadEnterpriseReply', $result->body->name());
        $this->assertSame([], $result->body->elements());
    }

    #[Test]
    public function full_header(): void
    {
        // Arrange
        $xml = <<<'XML'
        <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
           <soap:Header>
              <ReplyContext xmlns:ns2="http://economie.fgov.be/kbopub/webservices/v1/datamodel" xmlns="http://economie.fgov.be/kbopub/webservices/v1/messages">
                 <RequestId>Test-1</RequestId>
                 <ReplyId>104da2dd-c126-4604-b07d-4d7c5b86cba1</ReplyId>
                 <ReplyStatus>
                    <Code>00001</Code>
                    <Description>The operation completed successfully</Description>
                 </ReplyStatus>
                 <AccountBalance>7829</AccountBalance>
                 <SnapshotDate>2020-05-15</SnapshotDate>
              </ReplyContext>
           </soap:Header>
           <soap:Body>
              <ReadEnterpriseReply xmlns:ns2="http://economie.fgov.be/kbopub/webservices/v1/datamodel" xmlns="http://economie.fgov.be/kbopub/webservices/v1/messages">
                 <ns2:Enterprise><ns2:Number>314595348</ns2:Number></ns2:Enterprise>
              </ReadEnterpriseReply>
           </soap:Body>
        </soap:Envelope>
        XML;

        // Act
        $result = Envelope::unwrap($xml);

        // Assert
        $this->assertSame(ReplyStatus::Success, $result->context->status);
        $this->assertSame(7829, $result->context->accountBalance);
        $this->assertSame('2020-05-15', $result->context->snapshotDate?->toString());
        $this->assertSame('314595348', $result->body->child('Enterprise')->textOf('Number'));
    }

    #[Test]
    public function unknown_status_keeps_the_raw_code(): void
    {
        // Act
        $result = Envelope::unwrap(self::reply('<ReplyStatus><Code>12345</Code><Description>New</Description></ReplyStatus>', '<R/>'));

        // Assert
        $this->assertSame(ReplyStatus::Unknown, $result->context->status);
        $this->assertSame('12345', $result->context->statusCode);
        $this->assertFalse($result->context->charged());
    }

    #[Test]
    #[DataProvider('unexpected')]
    public function unexpected_bodies_are_unknown(string $xml): void
    {
        // Assert
        $this->expectException(UnknownException::class);

        // Act
        Envelope::unwrap($xml);
    }

    public static function unexpected(): iterable
    {
        yield 'proxy html' => ['<html><body>502 Bad Gateway'];
        yield 'empty' => [''];
        yield 'well-formed html' => ['<html><body><h1>502 Bad Gateway</h1></body></html>'];
        yield 'no reply context' => ['<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Header/><soap:Body><R/></soap:Body></soap:Envelope>'];
        yield 'empty body' => [self::reply('<ReplyStatus><Code>00001</Code><Description>ok</Description></ReplyStatus>', '')];
        yield 'no status' => [self::reply('', '<R/>')];
        yield 'malformed snapshot date' => [self::reply('<ReplyStatus><Code>00001</Code><Description>ok</Description></ReplyStatus><AccountBalance>1</AccountBalance><SnapshotDate>2020-13-45</SnapshotDate>', '<R/>')];
    }

    private static function reply(string $status, string $body): string
    {
        return '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Header><ReplyContext>'
            . "<RequestId>id</RequestId><ReplyId>rid</ReplyId>{$status}</ReplyContext></soap:Header>"
            . "<soap:Body>{$body}</soap:Body></soap:Envelope>";
    }
}
