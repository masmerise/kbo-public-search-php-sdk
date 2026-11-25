<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Type\Parameter;

use InvalidArgumentException;
use KboPublicSearch\Core\Type\Parameter\Phonemes;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class PhonemesTest extends TestCase
{
    #[Test]
    public function keeps_the_phonemes_in_order(): void
    {
        $this->assertSame(['economie', 'energie'], Phonemes::of('economie', 'energie')->toArray());
    }

    #[Test]
    public function at_least_one_phoneme_is_required(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Phonemes::of();
    }

    #[Test]
    public function at_most_ten_phonemes_are_allowed(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Phonemes::of(...array_fill(0, 11, 'a'));
    }

    #[Test]
    public function a_phoneme_is_at_most_160_characters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Phonemes::of(str_repeat('a', 161));
    }

    #[Test]
    public function a_phoneme_must_not_be_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Phonemes::of('');
    }
}
