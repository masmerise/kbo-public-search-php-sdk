<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Type\Primitive;

use Closure;
use InvalidArgumentException;
use KboPublicSearch\Core\Type\Primitive\MunicipalityCode;
use KboPublicSearch\Core\Type\Primitive\StreetName;
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\Entity\Type\Primitive\HouseNumber;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class RequestPrimitivesTest extends TestCase
{
    #[Test]
    public function valid_values_are_kept(): void
    {
        $this->assertSame('1210', Zipcode::fromString('1210')->toString());
        $this->assertSame('21014', MunicipalityCode::fromString('21014')->toString());
        $this->assertSame('veld', StreetName::fromString('veld')->toString());
        $this->assertSame('50', HouseNumber::fromString('50')->toString());
        $this->assertSame('12345678901', HouseNumber::fromString('12345678901')->toString());
    }

    /** @param Closure(): mixed $build */
    #[Test]
    #[DataProvider('invalid')]
    public function invalid_values_throw(Closure $build): void
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Act
        $build();
    }

    public static function invalid(): iterable
    {
        yield 'foreign zipcode' => [fn () => Zipcode::fromString('EH36AQ')];
        yield 'short zipcode' => [fn () => Zipcode::fromString('123')];
        yield 'short municipality code' => [fn () => MunicipalityCode::fromString('2101')];
        yield 'one-letter street' => [fn () => StreetName::fromString('v')];
        yield 'long street' => [fn () => StreetName::fromString(str_repeat('a', 33))];
        yield 'empty house number' => [fn () => HouseNumber::fromString('')];
        yield 'long house number' => [fn () => HouseNumber::fromString('123456789012')];
    }
}
