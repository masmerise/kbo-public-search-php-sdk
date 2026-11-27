<?php declare(strict_types=1);

namespace KboPublicSearch\Localization;

/** The language(s) in which KBO returns descriptions. */
final readonly class Languages
{
    /** @param list<Language> $languages */
    private function __construct(private array $languages) {}

    public static function dutch(): self
    {
        return new self([Language::Dutch]);
    }

    public static function dutchAndFrench(): self
    {
        return new self([Language::Dutch, Language::French]);
    }

    public static function french(): self
    {
        return new self([Language::French]);
    }

    /** @return list<string> */
    public function toArray(): array
    {
        return array_map(static fn (Language $language) => $language->value, $this->languages);
    }
}
