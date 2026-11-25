<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Xml;

/** @internal */
final readonly class Node
{
    /**
     * @param string|list<Node> $content
     * @param array<string, string> $attributes
     */
    private function __construct(
        public string $name,
        public string|array $content,
        public array $attributes,
    ) {}

    /**
     * @param string $name The qualified element name, e.g. "mes:Phoneme".
     * @param string|list<Node> $content Text, or child nodes.
     * @param array<string, string> $attributes
     */
    public static function element(string $name, string|array $content = [], array $attributes = []): self
    {
        return new self($name, $content, $attributes);
    }
}
