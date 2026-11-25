<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type;

use KboPublicSearch\Localization\Language;
use Stringable;

/** A description in each language KBO returned it in. */
final readonly class LocalizedText implements Stringable
{
    /** @param array<string, string> $byLanguage */
    private function __construct(private array $byLanguage) {}

    /** @param array<string, string> $byLanguage language code => text */
    public static function of(array $byLanguage): self
    {
        return new self($byLanguage);
    }

    /** @return array<string, string> */
    public function all(): array
    {
        return $this->byLanguage;
    }

    public function first(): ?string
    {
        return array_first($this->byLanguage);
    }

    public function in(Language $language): ?string
    {
        return $this->byLanguage[$language->value] ?? null;
    }

    public function __toString(): string
    {
        return $this->first() ?? '';
    }
}
