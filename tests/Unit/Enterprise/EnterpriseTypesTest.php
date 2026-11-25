<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Enterprise;

use KboPublicSearch\Enterprise\Type\EnterpriseTypes;
use KboPublicSearch\Enterprise\Type\TypeOfEnterprise;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class EnterpriseTypesTest extends TestCase
{
    #[Test]
    public function named_constructors(): void
    {
        $this->assertSame([TypeOfEnterprise::LegalPerson, TypeOfEnterprise::NaturalPerson], EnterpriseTypes::all()->toArray());
        $this->assertSame([TypeOfEnterprise::LegalPerson], EnterpriseTypes::legalPersons()->toArray());
        $this->assertSame([TypeOfEnterprise::NaturalPerson], EnterpriseTypes::naturalPersons()->toArray());
    }

    #[Test]
    public function legal_persons_only(): void
    {
        $this->assertTrue(EnterpriseTypes::legalPersons()->isLegalPersonsOnly());
        $this->assertFalse(EnterpriseTypes::all()->isLegalPersonsOnly());
        $this->assertFalse(EnterpriseTypes::naturalPersons()->isLegalPersonsOnly());
    }
}
