<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Xml;

use DOMElement;
use RuntimeException;

/**
 * A namespace-agnostic view on an XML element: children are matched by local name, direct children only.
 *
 * @internal
 */
final readonly class Element
{
    public function __construct(private DOMElement $element) {}

    /** @throws RuntimeException */
    public function child(string $name): Element
    {
        return $this->optional($name) ?? throw new RuntimeException("Element [{$name}] not found.");
    }

    /** @return list<Element> */
    public function children(string $name): array
    {
        return array_values(array_filter($this->elements(), static fn (Element $e) => $e->name() === $name));
    }

    /** @return list<Element> */
    public function elements(): array
    {
        $elements = [];

        foreach ($this->element->childNodes as $node) {
            if ($node instanceof DOMElement) {
                $elements[] = new Element($node);
            }
        }

        return $elements;
    }

    public function has(string $name): bool
    {
        return $this->optional($name) !== null;
    }

    public function name(): string
    {
        return $this->element->localName ?? $this->element->nodeName;
    }

    public function optional(string $name): ?Element
    {
        foreach ($this->element->childNodes as $node) {
            if ($node instanceof DOMElement && ($node->localName ?? $node->nodeName) === $name) {
                return new Element($node);
            }
        }

        return null;
    }

    public function optionalText(string $name): ?string
    {
        $text = $this->optional($name)?->text();

        return $text === null || $text === '' ? null : $text;
    }

    public function text(): string
    {
        return trim($this->element->textContent);
    }

    /** @throws RuntimeException */
    public function textOf(string $name): string
    {
        return $this->child($name)->text();
    }
}
