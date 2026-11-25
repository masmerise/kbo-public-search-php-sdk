<?php declare(strict_types=1);

namespace KboPublicSearch\Establishment\Type\Mapping;

use KboPublicSearch\Core\Type\Mapping\MapsActivities;
use KboPublicSearch\Core\Type\Mapping\MapsAddresses;
use KboPublicSearch\Core\Type\Mapping\MapsAuthorizations;
use KboPublicSearch\Core\Type\Mapping\MapsContactInformation;
use KboPublicSearch\Core\Type\Mapping\MapsDenominations;
use KboPublicSearch\Core\Type\Mapping\MapsExOfficioStrikingOffs;
use KboPublicSearch\Core\Type\Mapping\MapsLocalizedTexts;
use KboPublicSearch\Core\Type\Mapping\MapsPeriods;
use KboPublicSearch\Core\Type\Mapping\MapsStatuses;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Core\Type\Primitive\EstablishmentNumber;
use KboPublicSearch\Core\Xml\Element;
use KboPublicSearch\Establishment\Type\Establishment;
use KboPublicSearch\Establishment\Type\Establishments;

use function KboPublicSearch\Core\Utility\transform;

/** @internal */
trait MapsEstablishments
{
    use MapsActivities;
    use MapsAddresses;
    use MapsAuthorizations;
    use MapsContactInformation;
    use MapsDenominations;
    use MapsExOfficioStrikingOffs;
    use MapsLocalizedTexts;
    use MapsPeriods;
    use MapsStatuses;

    private function toEstablishment(Element $establishment): Establishment
    {
        return new Establishment(
            number: EstablishmentNumber::fromString($establishment->textOf('EstablishmentNumber')),
            status: $this->toStatus($establishment->child('Status')),
            period: $this->toRequiredPeriod($establishment, 'Period'),
            enterpriseNumber: transform($establishment->optionalText('EnterpriseNumber'), EnterpriseNumber::fromString(...)),
            enterpriseStatus: transform($establishment->optional('EnterpriseStatus'), $this->toStatus(...)),
            name: $this->toLocalizedText($establishment, 'PrimaryDenomination'),
            denominations: $this->toDenominations($establishment),
            contactInformation: $this->toContactInformations($establishment),
            address: transform($establishment->optional('Address'), $this->toAddress(...)),
            activities: $this->toActivities($establishment),
            authorizations: $this->toAuthorizations($establishment),
            enterpriseExOfficioStrikingOffs: $this->toExOfficioStrikingOffs($establishment, 'EnterpriseExOfficioStrikingOff'),
        );
    }

    private function toEstablishments(Element $parent): Establishments
    {
        return Establishments::of(array_map($this->toEstablishment(...), $parent->children('Establishment')));
    }
}
