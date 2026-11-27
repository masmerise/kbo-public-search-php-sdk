<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Denomination\Denomination;
use KboPublicSearch\Core\Type\Denomination\DenominationCode;
use KboPublicSearch\Core\Type\Denomination\Denominations;
use KboPublicSearch\Core\Xml\Element;
use KboPublicSearch\Localization\Language;

use function KboPublicSearch\Core\Utility\transform;

/** @internal */
final readonly class DenominationMapper
{
    public function __construct(
        private PeriodMapper $periodMapper = new PeriodMapper(),
    ) {}

    private function toDenomination(Element $denomination): Denomination
    {
        $description = $denomination->child('Description');

        return new Denomination(
            code: DenominationCode::from($denomination->textOf('Code')),
            name: preg_replace('/\s+/', ' ', $description->optionalText('Value') ?? '') ?? '',
            language: transform($description->optionalText('Language'), Language::from(...)),
            validityPeriod: $this->periodMapper->toOptionalPeriod($denomination, 'ValidityPeriod'),
        );
    }

    public function toDenominations(Element $parent): Denominations
    {
        return Denominations::of(array_map($this->toDenomination(...), $parent->children('Denomination')));
    }
}
