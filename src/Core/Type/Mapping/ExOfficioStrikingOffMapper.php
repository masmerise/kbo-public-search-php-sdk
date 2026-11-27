<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Mapping;

use KboPublicSearch\Core\Type\ExOfficioStrikingOff\ExOfficioStrikingOff;
use KboPublicSearch\Core\Type\ExOfficioStrikingOff\ExOfficioStrikingOffCode;
use KboPublicSearch\Core\Type\ExOfficioStrikingOff\ExOfficioStrikingOffs;
use KboPublicSearch\Core\Xml\Element;

/** @internal */
final readonly class ExOfficioStrikingOffMapper
{
    public function __construct(
        private DescriptionMapper $descriptionMapper = new DescriptionMapper(),
        private PeriodMapper $periodMapper = new PeriodMapper(),
    ) {}

    private function toExOfficioStrikingOff(Element $strikingOff): ExOfficioStrikingOff
    {
        return new ExOfficioStrikingOff(
            code: ExOfficioStrikingOffCode::from($strikingOff->textOf('Code')),
            description: $this->descriptionMapper->toDescription($strikingOff),
            validityPeriod: $this->periodMapper->toOptionalPeriod($strikingOff, 'ValidityPeriod'),
        );
    }

    /** @param string $element "ExOfficioStrikingOff", or "EnterpriseExOfficioStrikingOff" on establishments */
    public function toExOfficioStrikingOffs(Element $parent, string $element = 'ExOfficioStrikingOff'): ExOfficioStrikingOffs
    {
        return ExOfficioStrikingOffs::of(array_map($this->toExOfficioStrikingOff(...), $parent->children($element)));
    }
}
