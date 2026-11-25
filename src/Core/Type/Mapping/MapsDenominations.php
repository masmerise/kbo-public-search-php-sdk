<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Shared\Denomination;
use KboPublicSearch\Core\Type\Shared\DenominationKind;
use KboPublicSearch\Core\Type\Shared\Denominations;
use KboPublicSearch\Core\Xml\Element;

use function KboPublicSearch\Core\Utility\transform;

/** @internal */
trait MapsDenominations
{
    use MapsPeriods;

    private function toDenomination(Element $denomination): Denomination
    {
        $description = $denomination->child('Description');

        return new Denomination(
            kind: DenominationKind::from($denomination->textOf('Code')),
            name: preg_replace('/\s+/', ' ', $description->optionalText('Value') ?? '') ?? '',
            language: $description->optionalText('Language') ?? '',
            since: transform($denomination->optional('ValidityPeriod'), fn (Element $period) => $this->toDate($period, 'Begin')),
        );
    }

    private function toDenominations(Element $parent): Denominations
    {
        return Denominations::of(array_map($this->toDenomination(...), $parent->children('Denomination')));
    }
}
