<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type\Mapping;

use KboPublicSearch\Core\Type\Mapping\AddressMapper;
use KboPublicSearch\Core\Type\Mapping\DescriptionMapper;
use KboPublicSearch\Core\Type\Mapping\PeriodMapper;
use KboPublicSearch\Core\Type\Mapping\StatusMapper;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Core\Type\TypeOfEnterprise;
use KboPublicSearch\Core\Xml\Element;
use KboPublicSearch\Enterprise\Type\EnterpriseSummaries;
use KboPublicSearch\Enterprise\Type\EnterpriseSummary;
use KboPublicSearch\Enterprise\Type\JuridicalForm;
use KboPublicSearch\Enterprise\Type\JuridicalFormCode;

use function KboPublicSearch\Core\Utility\transform;

/** @internal */
final readonly class EnterpriseSummaryMapper
{
    public function __construct(
        private StatusMapper $statusMapper = new StatusMapper(),
        private PeriodMapper $periodMapper = new PeriodMapper(),
        private DescriptionMapper $descriptionMapper = new DescriptionMapper(),
        private AddressMapper $addressMapper = new AddressMapper(),
    ) {}

    private function toEnterpriseSummary(Element $enterprise): EnterpriseSummary
    {
        $status = $enterprise->optional('Status') ?? $enterprise->optional('JuridicalSituation')?->optional('Status');

        return new EnterpriseSummary(
            number: EnterpriseNumber::fromString($enterprise->textOf('Number')),
            status: transform($status, $this->statusMapper->toStatus(...)),
            period: $this->periodMapper->toRequiredPeriod($enterprise, 'Period'),
            typeOfEnterprise: TypeOfEnterprise::from($enterprise->textOf('TypeOfEnterprise')),
            juridicalForm: transform($enterprise->optional('JuridicalForm'), fn (Element $form) => new JuridicalForm(
                JuridicalFormCode::from($form->textOf('Code')),
                $this->descriptionMapper->toDescription($form),
                $this->periodMapper->toOptionalPeriod($form, 'ValidityPeriod'),
                null,
            )),
            primaryDenomination: $this->descriptionMapper->toDescription($enterprise, 'PrimaryDenomination'),
            address: transform($enterprise->optional('Address'), $this->addressMapper->toAddress(...)),
            businessUnits: (int) ($enterprise->optionalText('BusinessUnits') ?? 0),
        );
    }

    public function toEnterpriseSummaries(Element $parent): EnterpriseSummaries
    {
        return EnterpriseSummaries::of(array_map($this->toEnterpriseSummary(...), $parent->children('Enterprise')));
    }
}
