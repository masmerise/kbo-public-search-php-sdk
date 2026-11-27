<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Type\Primitive;

use InvalidArgumentException;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class EnterpriseNumberTest extends TestCase
{
    #[Test]
    #[DataProvider('valid')]
    public function it_normalises_valid_numbers(string $input, string $expected): void
    {
        // Act
        $number = EnterpriseNumber::fromString($input);

        // Assert
        $this->assertSame($expected, $number->toString());
        $this->assertSame($expected, (string) $number);
    }

    public static function valid(): iterable
    {
        yield 'dotted' => ['0314.595.348', '0314595348'];
        yield 'BE prefix' => ['BE0314595348', '0314595348'];
        yield 'lowercase be prefix with spaces' => ['be 0314 595 348', '0314595348'];
        yield 'nine digits (as in replies)' => ['314595348', '0314595348'];
        yield 'canceled example' => ['0206231995', '0206231995'];
        yield 'starts with 1' => ['1037.799.832', '1037799832'];
    }

    #[Test]
    #[DataProvider('invalid')]
    public function it_rejects_invalid_numbers(string $input): void
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Act
        EnterpriseNumber::fromString($input);
    }

    public static function invalid(): iterable
    {
        yield 'checksum' => ['0314595349'];
        yield 'too short' => ['123'];
        yield 'text' => ['sometext'];
        yield 'starts with 2 (establishment)' => ['2147241191'];
        yield 'empty' => [''];
    }

    #[Test]
    public function equal_numbers_are_equal(): void
    {
        $this->assertTrue(EnterpriseNumber::fromString('314595348')->equals(EnterpriseNumber::fromString('BE0314.595.348')));
    }
}
