<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Type\Primitive;

use InvalidArgumentException;
use KboPublicSearch\Core\Type\Primitive\EstablishmentNumber;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class EstablishmentNumberTest extends TestCase
{
    #[Test]
    public function it_normalises_valid_numbers(): void
    {
        $this->assertSame('2147241191', EstablishmentNumber::fromString('2.147.241.191')->toString());
    }

    #[Test]
    #[DataProvider('invalid')]
    public function it_rejects_invalid_numbers(string $input): void
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Act
        EstablishmentNumber::fromString($input);
    }

    public static function invalid(): iterable
    {
        yield 'checksum' => ['2147241192'];
        yield 'enterprise number' => ['0314595348'];
        yield 'empty' => [''];
    }
}
