<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Xml;

use KboPublicSearch\Core\Xml\Reader;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;

final class ReaderTest extends TestCase
{
    #[Test]
    public function matches_by_local_name(): void
    {
        // Arrange
        $xml = <<<'XML'
        <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
           <soap:Header>
              <ReplyContext xmlns:ns2="http://economie.fgov.be/kbopub/webservices/v1/datamodel" xmlns="http://economie.fgov.be/kbopub/webservices/v1/messages">
                 <RequestId>myid</RequestId>
                 <ReplyId>23efbf0d-7d1e-4f67-981e-e1e2a766a5e0</ReplyId>
              </ReplyContext>
           </soap:Header>
        </soap:Envelope>
        XML;

        // Act
        $envelope = Reader::fromString($xml);

        // Assert
        $this->assertSame('Envelope', $envelope->name());
        $this->assertSame('myid', $envelope->child('Header')->child('ReplyContext')->textOf('RequestId'));
        $this->assertTrue($envelope->has('Header'));
        $this->assertFalse($envelope->has('Body'));
        $this->assertNull($envelope->optional('Body'));
    }

    #[Test]
    public function only_direct_children_match(): void
    {
        // Arrange
        $root = Reader::fromString('<a><b><Code>x</Code></b></a>');

        // Assert
        $this->assertFalse($root->has('Code'));
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Element [Code] not found.');

        // Act
        $root->child('Code');
    }

    #[Test]
    public function optional_text_is_null_for_empty(): void
    {
        $this->assertNull(Reader::fromString('<a><Code/></a>')->optionalText('Code'));
        $this->assertNull(Reader::fromString('<a><Code></Code></a>')->optionalText('Code'));
        $this->assertNull(Reader::fromString('<a><Code>  </Code></a>')->optionalText('Code'));
        $this->assertNull(Reader::fromString('<a/>')->optionalText('Code'));
        $this->assertSame('0086', Reader::fromString('<a><Code> 0086 </Code></a>')->optionalText('Code'));
    }

    #[Test]
    public function children_returns_all_same_named(): void
    {
        // Arrange
        $xml = '<ns2:Enterprise xmlns:ns2="urn:d"><ns2:Denomination>1</ns2:Denomination><ns2:Number>2</ns2:Number>'
            . '<ns2:Denomination>3</ns2:Denomination><ns2:Denomination>4</ns2:Denomination></ns2:Enterprise>';

        // Act
        $enterprise = Reader::fromString($xml);

        // Assert
        $this->assertCount(3, $enterprise->children('Denomination'));
        $this->assertSame('3', $enterprise->children('Denomination')[1]->text());
        $this->assertCount(4, $enterprise->elements());
        $this->assertSame([], $enterprise->children('Missing'));
    }

    #[Test]
    #[DataProvider('doctypes')]
    public function document_type_declarations_are_rejected(string $xml): void
    {
        // Assert: SOAP 1.1 forbids DTDs, so a reply carrying one is never a KBO reply.
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Response must not contain a document type declaration.');

        // Act
        Reader::fromString($xml);
    }

    public static function doctypes(): iterable
    {
        yield 'external entity' => ['<?xml version="1.0"?><!DOCTYPE a [<!ENTITY x SYSTEM "file:///etc/hosts">]><a>&x;</a>'];
        yield 'internal entity' => ['<!DOCTYPE a [<!ENTITY x "y">]><a>&x;</a>'];
        yield 'html doctype' => ['<!DOCTYPE html><html><body>502</body></html>'];
    }

    #[Test]
    #[DataProvider('invalid')]
    public function invalid_xml_throws(string $xml): void
    {
        // Assert
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Response is not valid XML.');

        // Act
        Reader::fromString($xml);
    }

    public static function invalid(): iterable
    {
        yield 'unclosed html' => ['<html>'];
        yield 'empty' => [''];
        yield 'proxy page' => ['<html><body>502 Bad Gateway'];
        yield 'plain text' => ['Service Unavailable'];
    }
}
