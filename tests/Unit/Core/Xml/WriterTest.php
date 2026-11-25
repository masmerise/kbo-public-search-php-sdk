<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Xml;

use KboPublicSearch\Core\Xml\Node;
use KboPublicSearch\Core\Xml\Writer;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class WriterTest extends TestCase
{
    #[Test]
    public function writes_prefixed_repeated_elements_and_attributes(): void
    {
        // Arrange
        $root = Node::element('mes:R', [
            Node::element('mes:Phoneme', 'a'),
            Node::element('mes:Phoneme', 'b & c'),
            Node::element('x:P', 'v', ['Type' => 't']),
        ]);

        // Act
        $xml = Writer::write($root, ['mes' => 'urn:m', 'x' => 'urn:x']);

        // Assert
        $this->assertStringStartsWith('<?xml version="1.0" encoding="UTF-8"?>', $xml);
        $this->assertStringContainsString('<mes:R xmlns:mes="urn:m" xmlns:x="urn:x">', $xml);
        $this->assertStringContainsString('<mes:Phoneme>a</mes:Phoneme><mes:Phoneme>b &amp; c</mes:Phoneme>', $xml);
        $this->assertStringContainsString('<x:P Type="t">v</x:P>', $xml);
    }

    #[Test]
    public function writes_empty_elements(): void
    {
        // Act
        $xml = Writer::write(Node::element('mes:R'), ['mes' => 'urn:m']);

        // Assert
        $this->assertStringContainsString('<mes:R xmlns:mes="urn:m"/>', $xml);
    }
}
