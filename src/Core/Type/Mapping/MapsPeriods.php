<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Primitive\Date;
use KboPublicSearch\Core\Type\Shared\Period;
use KboPublicSearch\Core\Xml\Element;

use function KboPublicSearch\Core\Utility\transform;

/** @internal */
trait MapsPeriods
{
    private function toDate(Element $parent, string $element): ?Date
    {
        return transform($parent->optionalText($element), Date::fromString(...));
    }

    private function toPeriod(Element $period): Period
    {
        return new Period(
            begin: $this->toDate($period, 'Begin'),
            end: $this->toDate($period, 'End'),
        );
    }

    private function toOptionalPeriod(Element $parent, string $element): ?Period
    {
        return transform($parent->optional($element), $this->toPeriod(...));
    }

    /** For elements the XSD requires; an absent one maps to an empty period rather than failing the reply. */
    private function toRequiredPeriod(Element $parent, string $element): Period
    {
        return $this->toOptionalPeriod($parent, $element) ?? new Period(null, null);
    }
}
