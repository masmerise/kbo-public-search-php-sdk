<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Entity;

use KboPublicSearch\Entity\Type\EntityFilter;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class EntityFilterTest extends TestCase
{
    #[Test]
    public function named_constructors(): void
    {
        $this->assertSame([true, true, true], self::flags(EntityFilter::all()));
        $this->assertSame([true, false, false], self::flags(EntityFilter::physicalPersons()));
        $this->assertSame([false, true, false], self::flags(EntityFilter::legalPersons()));
        $this->assertSame([false, false, true], self::flags(EntityFilter::establishments()));
    }

    #[Test]
    public function kinds_combine(): void
    {
        $this->assertSame([false, true, true], self::flags(EntityFilter::legalPersons()->with(EntityFilter::establishments())));
        $this->assertSame([true, true, false], self::flags(EntityFilter::physicalPersons()->with(EntityFilter::legalPersons())));
    }

    /** @return array{bool, bool, bool} */
    private static function flags(EntityFilter $kinds): array
    {
        return [$kinds->enterprisePhysicalPerson, $kinds->enterpriseLegalPerson, $kinds->establishment];
    }
}
