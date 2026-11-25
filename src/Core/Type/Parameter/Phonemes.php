<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Parameter;

use Webmozart\Assert\Assert;

/** 1 to 10 words to search phonetically for. */
final readonly class Phonemes
{
    /** @param list<string> $phonemes */
    private function __construct(private array $phonemes)
    {
        Assert::countBetween($phonemes, 1, 10, 'Between 1 and 10 phonemes must be given.');
        Assert::allLengthBetween($phonemes, 1, 160, 'A phoneme must be between 1 and 160 characters.');
    }

    public static function of(string ...$phonemes): self
    {
        return new self(array_values($phonemes));
    }

    /** @return list<string> */
    public function toArray(): array
    {
        return $this->phonemes;
    }
}
