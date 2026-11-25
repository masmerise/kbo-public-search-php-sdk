<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Xml;

use XMLWriter;

/** @internal */
final readonly class Writer
{
    /** @param array<string, string> $namespaces prefix => uri, declared on the root element */
    public static function write(Node $root, array $namespaces = []): string
    {
        $writer = new XMLWriter();
        $writer->openMemory();
        $writer->startDocument('1.0', 'UTF-8');

        self::node($writer, $root, $namespaces);

        $writer->endDocument();

        return $writer->outputMemory();
    }

    /** @param array<string, string> $namespaces */
    private static function node(XMLWriter $writer, Node $node, array $namespaces = []): void
    {
        $writer->startElement($node->name);

        foreach ($namespaces as $prefix => $uri) {
            $writer->writeAttribute("xmlns:{$prefix}", $uri);
        }

        foreach ($node->attributes as $name => $value) {
            $writer->writeAttribute($name, $value);
        }

        if (is_string($node->content)) {
            $writer->text($node->content);
        } else {
            foreach ($node->content as $child) {
                self::node($writer, $child);
            }
        }

        $writer->endElement();
    }
}
