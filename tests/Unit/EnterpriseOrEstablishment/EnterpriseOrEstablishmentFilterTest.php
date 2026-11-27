<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\EnterpriseOrEstablishment;

use KboPublicSearch\EnterpriseOrEstablishment\Type\Parameter\EnterpriseOrEstablishmentFilter;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class EnterpriseOrEstablishmentFilterTest extends TestCase
{
    #[Test]
    public function named_constructors(): void
    {
        $this->assertSame([true, true, true], self::flags(EnterpriseOrEstablishmentFilter::all()));
        $this->assertSame([true, false, false], self::flags(EnterpriseOrEstablishmentFilter::physicalPersons()));
        $this->assertSame([false, true, false], self::flags(EnterpriseOrEstablishmentFilter::legalPersons()));
        $this->assertSame([false, false, true], self::flags(EnterpriseOrEstablishmentFilter::establishments()));
    }

    #[Test]
    public function kinds_combine(): void
    {
        $this->assertSame([false, true, true], self::flags(EnterpriseOrEstablishmentFilter::legalPersons()->with(EnterpriseOrEstablishmentFilter::establishments())));
        $this->assertSame([true, true, false], self::flags(EnterpriseOrEstablishmentFilter::physicalPersons()->with(EnterpriseOrEstablishmentFilter::legalPersons())));
    }

    /** @return array{bool, bool, bool} */
    private static function flags(EnterpriseOrEstablishmentFilter $kinds): array
    {
        return [$kinds->enterprisePhysicalPerson, $kinds->enterpriseLegalPerson, $kinds->establishment];
    }
}
