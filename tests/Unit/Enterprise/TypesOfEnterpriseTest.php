<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Enterprise;

use KboPublicSearch\Core\Type\TypeOfEnterprise;
use KboPublicSearch\Enterprise\Type\Parameter\TypesOfEnterprise;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class TypesOfEnterpriseTest extends TestCase
{
    #[Test]
    public function named_constructors(): void
    {
        $this->assertSame([TypeOfEnterprise::LegalPerson, TypeOfEnterprise::PhysicalPerson], TypesOfEnterprise::all()->toArray());
        $this->assertSame([TypeOfEnterprise::LegalPerson], TypesOfEnterprise::legalPersons()->toArray());
        $this->assertSame([TypeOfEnterprise::PhysicalPerson], TypesOfEnterprise::physicalPersons()->toArray());
    }

    #[Test]
    public function legal_persons_only(): void
    {
        $this->assertTrue(TypesOfEnterprise::legalPersons()->isLegalPersonsOnly());
        $this->assertFalse(TypesOfEnterprise::all()->isLegalPersonsOnly());
        $this->assertFalse(TypesOfEnterprise::physicalPersons()->isLegalPersonsOnly());
    }
}
