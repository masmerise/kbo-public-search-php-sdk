<?php declare(strict_types=1);

namespace KboPublicSearch\Entity\Type;

use KboPublicSearch\Core\Type\Collection;

/**
 * Enterprises and establishment units, in reply order.
 *
 * @extends Collection<Enterprise|Establishment>
 */
final readonly class Entities extends Collection
{
    public function enterprises(): Enterprises
    {
        return Enterprises::of(array_values(array_filter($this->items, static fn ($entity) => $entity instanceof Enterprise)));
    }

    public function establishments(): Establishments
    {
        return Establishments::of(array_values(array_filter($this->items, static fn ($entity) => $entity instanceof Establishment)));
    }
}
