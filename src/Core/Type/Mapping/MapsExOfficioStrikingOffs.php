<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Mapping;

use KboPublicSearch\Core\Type\StrikingOff\ExOfficioStrikingOff;
use KboPublicSearch\Core\Type\StrikingOff\ExOfficioStrikingOffs;
use KboPublicSearch\Core\Xml\Element;

use function KboPublicSearch\Core\Utility\transform;

/** @internal */
trait MapsExOfficioStrikingOffs
{
    use MapsLocalizedTexts;
    use MapsPeriods;

    private function toExOfficioStrikingOff(Element $strikingOff): ExOfficioStrikingOff
    {
        return new ExOfficioStrikingOff(
            code: $strikingOff->textOf('Code'),
            description: $this->toLocalizedText($strikingOff),
            since: transform($strikingOff->optional('ValidityPeriod'), fn (Element $period) => $this->toDate($period, 'Begin')),
        );
    }

    /** @param string $element "ExOfficioStrikingOff", or "EnterpriseExOfficioStrikingOff" on establishments */
    private function toExOfficioStrikingOffs(Element $parent, string $element = 'ExOfficioStrikingOff'): ExOfficioStrikingOffs
    {
        return ExOfficioStrikingOffs::of(array_map($this->toExOfficioStrikingOff(...), $parent->children($element)));
    }
}
