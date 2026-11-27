<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Type\Primitive;

use InvalidArgumentException;
use KboPublicSearch\Core\Type\Primitive\Date;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class DateTest extends TestCase
{
    #[Test]
    #[DataProvider('dates')]
    public function keeps_calendar_date(string $input, string $expected): void
    {
        // Act
        $date = Date::fromString($input);

        // Assert
        $this->assertSame($expected, $date->toString());
        $this->assertSame($expected . ' 00:00:00', $date->toDateTimeImmutable()->format('Y-m-d H:i:s'));
    }

    public static function dates(): iterable
    {
        yield 'positive offset' => ['1968-01-01+01:00', '1968-01-01'];
        yield 'zulu' => ['2002-03-05Z', '2002-03-05'];
        yield 'plain' => ['2014-02-19', '2014-02-19'];
        yield 'negative offset' => ['2006-10-04-11:00', '2006-10-04'];
        yield 'surrounding whitespace' => [' 2014-02-19 ', '2014-02-19'];
    }

    #[Test]
    #[DataProvider('invalid')]
    public function invalid_dates_throw(string $input): void
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Act
        Date::fromString($input);
    }

    public static function invalid(): iterable
    {
        yield 'five-digit year' => ['20025-09-01'];
        yield 'month 13' => ['2020-13-01'];
        yield 'february 30' => ['2020-02-30'];
        yield 'empty' => [''];
        yield 'timestamp' => ['2020-01-01T10:00:00'];
    }
}
