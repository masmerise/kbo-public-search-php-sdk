<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

use KboPublicSearch\Core\Type\TypeOfEnterprise;

/** Which types of enterprise a search returns. */
final readonly class TypesOfEnterprise
{
    /** @param list<TypeOfEnterprise> $types */
    private function __construct(private array $types) {}

    public static function all(): self
    {
        return new self([TypeOfEnterprise::LegalPerson, TypeOfEnterprise::PhysicalPerson]);
    }

    public static function legalPersons(): self
    {
        return new self([TypeOfEnterprise::LegalPerson]);
    }

    public static function physicalPersons(): self
    {
        return new self([TypeOfEnterprise::PhysicalPerson]);
    }

    public function isLegalPersonsOnly(): bool
    {
        return $this->types === [TypeOfEnterprise::LegalPerson];
    }

    /** @return list<TypeOfEnterprise> */
    public function toArray(): array
    {
        return $this->types;
    }
}
