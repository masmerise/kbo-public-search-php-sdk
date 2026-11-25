<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Entity;

use KboPublicSearch\Entity\Type\EntityKinds;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class EntityKindsTest extends TestCase
{
    #[Test]
    public function named_constructors(): void
    {
        $this->assertSame([true, true, true], self::flags(EntityKinds::all()));
        $this->assertSame([true, false, false], self::flags(EntityKinds::naturalPersons()));
        $this->assertSame([false, true, false], self::flags(EntityKinds::legalPersons()));
        $this->assertSame([false, false, true], self::flags(EntityKinds::establishments()));
    }

    #[Test]
    public function kinds_combine(): void
    {
        $this->assertSame([false, true, true], self::flags(EntityKinds::legalPersons()->with(EntityKinds::establishments())));
        $this->assertSame([true, true, false], self::flags(EntityKinds::naturalPersons()->with(EntityKinds::legalPersons())));
    }

    /** @return array{bool, bool, bool} */
    private static function flags(EntityKinds $kinds): array
    {
        return [$kinds->naturalPersons, $kinds->legalPersons, $kinds->establishments];
    }
}
