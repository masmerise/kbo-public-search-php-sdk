<?php declare(strict_types=1);

namespace KboPublicSearch\Entity\Type;

use KboPublicSearch\Core\Type\Collection;
use KboPublicSearch\Enterprise\Type\EnterpriseSummaries;
use KboPublicSearch\Enterprise\Type\EnterpriseSummary;
use KboPublicSearch\Establishment\Type\Establishment;
use KboPublicSearch\Establishment\Type\Establishments;

/**
 * Enterprises and establishment units, in reply order.
 *
 * @extends Collection<EnterpriseSummary|Establishment>
 */
final readonly class Entities extends Collection
{
    public function enterprises(): EnterpriseSummaries
    {
        return EnterpriseSummaries::of(array_values(array_filter($this->items, static fn ($entity) => $entity instanceof EnterpriseSummary)));
    }

    public function establishments(): Establishments
    {
        return Establishments::of(array_values(array_filter($this->items, static fn ($entity) => $entity instanceof Establishment)));
    }
}
