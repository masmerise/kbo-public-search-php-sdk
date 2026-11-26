<?php declare(strict_types=1);

namespace KboPublicSearch\Establishment\Type\Mapping;

use KboPublicSearch\Core\Type\Mapping\ActivityMapper;
use KboPublicSearch\Core\Type\Mapping\AddressMapper;
use KboPublicSearch\Core\Type\Mapping\AuthorizationMapper;
use KboPublicSearch\Core\Type\Mapping\ContactInformationMapper;
use KboPublicSearch\Core\Type\Mapping\DenominationMapper;
use KboPublicSearch\Core\Type\Mapping\DescriptionMapper;
use KboPublicSearch\Core\Type\Mapping\ExOfficioStrikingOffMapper;
use KboPublicSearch\Core\Type\Mapping\PeriodMapper;
use KboPublicSearch\Core\Type\Mapping\StatusMapper;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Core\Type\Primitive\EstablishmentNumber;
use KboPublicSearch\Core\Xml\Element;
use KboPublicSearch\Establishment\Type\Establishment;
use KboPublicSearch\Establishment\Type\Establishments;

use function KboPublicSearch\Core\Utility\transform;

/** @internal */
final readonly class EstablishmentMapper
{
    public function __construct(
        private StatusMapper $statusMapper = new StatusMapper(),
        private PeriodMapper $periodMapper = new PeriodMapper(),
        private DescriptionMapper $descriptionMapper = new DescriptionMapper(),
        private DenominationMapper $denominationMapper = new DenominationMapper(),
        private ContactInformationMapper $contactInformationMapper = new ContactInformationMapper(),
        private AddressMapper $addressMapper = new AddressMapper(),
        private ActivityMapper $activityMapper = new ActivityMapper(),
        private AuthorizationMapper $authorizationMapper = new AuthorizationMapper(),
        private ExOfficioStrikingOffMapper $exOfficioStrikingOffMapper = new ExOfficioStrikingOffMapper(),
    ) {}

    public function toEstablishment(Element $establishment): Establishment
    {
        return new Establishment(
            establishmentNumber: EstablishmentNumber::fromString($establishment->textOf('EstablishmentNumber')),
            status: $this->statusMapper->toStatus($establishment->child('Status')),
            period: $this->periodMapper->toRequiredPeriod($establishment, 'Period'),
            enterpriseNumber: transform($establishment->optionalText('EnterpriseNumber'), EnterpriseNumber::fromString(...)),
            enterpriseStatus: transform($establishment->optional('EnterpriseStatus'), $this->statusMapper->toStatus(...)),
            primaryDenomination: $this->descriptionMapper->toDescription($establishment, 'PrimaryDenomination'),
            denominations: $this->denominationMapper->toDenominations($establishment),
            contactInformations: $this->contactInformationMapper->toContactInformations($establishment),
            address: transform($establishment->optional('Address'), $this->addressMapper->toAddress(...)),
            activities: $this->activityMapper->toActivities($establishment),
            authorizations: $this->authorizationMapper->toAuthorizations($establishment),
            enterpriseExOfficioStrikingOffs: $this->exOfficioStrikingOffMapper->toExOfficioStrikingOffs($establishment, 'EnterpriseExOfficioStrikingOff'),
        );
    }

    public function toEstablishments(Element $parent): Establishments
    {
        return Establishments::of(array_map($this->toEstablishment(...), $parent->children('Establishment')));
    }
}
