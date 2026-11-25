<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Mapping;

use KboPublicSearch\Core\Type\ExOfficioStrikingOff\ExOfficioStrikingOff;
use KboPublicSearch\Core\Type\ExOfficioStrikingOff\ExOfficioStrikingOffs;
use KboPublicSearch\Core\Xml\Element;

/** @internal */
trait MapsExOfficioStrikingOffs
{
    use MapsDescriptions;
    use MapsPeriods;

    private function toExOfficioStrikingOff(Element $strikingOff): ExOfficioStrikingOff
    {
        return new ExOfficioStrikingOff(
            code: $strikingOff->textOf('Code'),
            description: $this->toDescription($strikingOff),
            validityPeriod: $this->toOptionalPeriod($strikingOff, 'ValidityPeriod'),
        );
    }

    /** @param string $element "ExOfficioStrikingOff", or "EnterpriseExOfficioStrikingOff" on establishments */
    private function toExOfficioStrikingOffs(Element $parent, string $element = 'ExOfficioStrikingOff'): ExOfficioStrikingOffs
    {
        return ExOfficioStrikingOffs::of(array_map($this->toExOfficioStrikingOff(...), $parent->children($element)));
    }
}
