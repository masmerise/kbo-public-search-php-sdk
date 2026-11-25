<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Denomination\Denomination;
use KboPublicSearch\Core\Type\Denomination\DenominationKind;
use KboPublicSearch\Core\Type\Denomination\Denominations;
use KboPublicSearch\Core\Xml\Element;

/** @internal */
trait MapsDenominations
{
    use MapsPeriods;

    private function toDenomination(Element $denomination): Denomination
    {
        $description = $denomination->child('Description');
        $code = $denomination->textOf('Code');

        return new Denomination(
            code: $code,
            kind: DenominationKind::tryFrom($code),
            name: preg_replace('/\s+/', ' ', $description->optionalText('Value') ?? '') ?? '',
            language: $description->optionalText('Language') ?? '',
            validityPeriod: $this->toOptionalPeriod($denomination, 'ValidityPeriod'),
        );
    }

    private function toDenominations(Element $parent): Denominations
    {
        return Denominations::of(array_map($this->toDenomination(...), $parent->children('Denomination')));
    }
}
