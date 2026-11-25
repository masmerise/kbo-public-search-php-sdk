<?php declare(strict_types=1);

namespace KboPublicSearch\Entity\Type\Mapping;

use KboPublicSearch\Core\Xml\Element;
use KboPublicSearch\Enterprise\Type\Mapping\MapsEnterpriseSummaries;
use KboPublicSearch\Entity\Type\Entities;
use KboPublicSearch\Establishment\Type\Mapping\MapsEstablishments;

/** @internal */
trait MapsEntities
{
    use MapsEnterpriseSummaries;
    use MapsEstablishments;

    private function toEntities(Element $parent): Entities
    {
        $entities = [];

        foreach ($parent->elements() as $element) {
            $entity = match ($element->name()) {
                'Enterprise' => $this->toEnterpriseSummary($element),
                'Establishment' => $this->toEstablishment($element),
                default => null,
            };

            if ($entity !== null) {
                $entities[] = $entity;
            }
        }

        return Entities::of($entities);
    }
}
