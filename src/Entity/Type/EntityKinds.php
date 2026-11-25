<?php declare(strict_types=1);

namespace KboPublicSearch\Entity\Type;

/** Which kinds of entity an activity search returns; at least one by construction. */
final readonly class EntityKinds
{
    private function __construct(
        public bool $naturalPersons,
        public bool $legalPersons,
        public bool $establishments,
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

    public static function naturalPersons(): self
    {
        return new self(true, false, false);
    }

    public function with(EntityKinds $other): self
    {
        return new self(
            $this->naturalPersons || $other->naturalPersons,
            $this->legalPersons || $other->legalPersons,
            $this->establishments || $other->establishments,
        );
    }
}
