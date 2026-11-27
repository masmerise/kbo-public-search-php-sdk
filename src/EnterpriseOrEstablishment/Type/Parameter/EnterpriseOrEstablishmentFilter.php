<?php declare(strict_types=1);

namespace KboPublicSearch\EnterpriseOrEstablishment\Type\Parameter;

/** Which kinds of entity an activity search returns; at least one by construction. */
final readonly class EnterpriseOrEstablishmentFilter
{
    private function __construct(
        public bool $enterprisePhysicalPerson,
        public bool $enterpriseLegalPerson,
        public bool $establishment,
    ) {}

    public static function all(): self
    {
        return new self(true, true, true);
    }

    public static function establishments(): self
    {
        return new self(false, false, true);
    }

    public static function legalPersons(): self
    {
        return new self(false, true, false);
    }

    public static function physicalPersons(): self
    {
        return new self(true, false, false);
    }

    public function with(EnterpriseOrEstablishmentFilter $other): self
    {
        return new self(
            $this->enterprisePhysicalPerson || $other->enterprisePhysicalPerson,
            $this->enterpriseLegalPerson || $other->enterpriseLegalPerson,
            $this->establishment || $other->establishment,
        );
    }
}
