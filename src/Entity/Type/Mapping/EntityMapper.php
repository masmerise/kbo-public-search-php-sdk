<?php declare(strict_types=1);

namespace KboPublicSearch\Entity\Type\Mapping;

use KboPublicSearch\Core\Type\Mapping\AddressMapper;
use KboPublicSearch\Core\Type\Mapping\DescriptionMapper;
use KboPublicSearch\Core\Type\Mapping\PeriodMapper;
use KboPublicSearch\Core\Type\Mapping\StatusMapper;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Core\Type\Primitive\EstablishmentNumber;
use KboPublicSearch\Core\Type\TypeOfEnterprise;
use KboPublicSearch\Core\Xml\Element;
use KboPublicSearch\Entity\Type\Enterprise;
use KboPublicSearch\Entity\Type\Entities;
use KboPublicSearch\Entity\Type\Establishment;

use function KboPublicSearch\Core\Utility\transform;

/** @internal */
final readonly class EntityMapper
{
    public function __construct(
        private StatusMapper $statusMapper = new StatusMapper(),
        private PeriodMapper $periodMapper = new PeriodMapper(),
        private DescriptionMapper $descriptionMapper = new DescriptionMapper(),
        private AddressMapper $addressMapper = new AddressMapper(),
    ) {}

    public function toEntities(Element $parent): Entities
    {
        $entities = [];

        foreach ($parent->elements() as $element) {
            $entity = match ($element->name()) {
                'Enterprise' => $this->toEntityEnterprise($element),
                'Establishment' => $this->toEntityEstablishment($element),
                default => null,
            };

            if ($entity !== null) {
                $entities[] = $entity;
            }
        }

        return Entities::of($entities);
    }

    private function toEntityEnterprise(Element $enterprise): Enterprise
    {
        $status = $enterprise->optional('Status') ?? $enterprise->optional('JuridicalSituation')?->optional('Status');

        return new Enterprise(
            number: EnterpriseNumber::fromString($enterprise->textOf('Number')),
            status: transform($status, $this->statusMapper->toStatus(...)),
            period: $this->periodMapper->toRequiredPeriod($enterprise, 'Period'),
            typeOfEnterprise: TypeOfEnterprise::from($enterprise->textOf('TypeOfEnterprise')),
            primaryDenomination: $this->descriptionMapper->toDescription($enterprise, 'PrimaryDenomination'),
            address: transform($enterprise->optional('Address'), $this->addressMapper->toAddress(...)),
            businessUnits: (int) ($enterprise->optionalText('BusinessUnits') ?? 0),
        );
    }

    private function toEntityEstablishment(Element $establishment): Establishment
    {
        return new Establishment(
            establishmentNumber: EstablishmentNumber::fromString($establishment->textOf('EstablishmentNumber')),
            status: $this->statusMapper->toStatus($establishment->child('Status')),
            period: $this->periodMapper->toRequiredPeriod($establishment, 'Period'),
            enterpriseNumber: transform($establishment->optionalText('EnterpriseNumber'), EnterpriseNumber::fromString(...)),
            enterpriseStatus: transform($establishment->optional('EnterpriseStatus'), $this->statusMapper->toStatus(...)),
            primaryDenomination: $this->descriptionMapper->toDescription($establishment, 'PrimaryDenomination'),
            address: transform($establishment->optional('Address'), $this->addressMapper->toAddress(...)),
        );
    }
}
