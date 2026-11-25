<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type\Mapping;

use KboPublicSearch\Core\Type\Mapping\MapsAddresses;
use KboPublicSearch\Core\Type\Mapping\MapsDescriptions;
use KboPublicSearch\Core\Type\Mapping\MapsPeriods;
use KboPublicSearch\Core\Type\Mapping\MapsStatuses;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Core\Xml\Element;
use KboPublicSearch\Enterprise\Type\EnterpriseSummaries;
use KboPublicSearch\Enterprise\Type\EnterpriseSummary;
use KboPublicSearch\Enterprise\Type\JuridicalForm;
use KboPublicSearch\Enterprise\Type\TypeOfEnterprise;

use function KboPublicSearch\Core\Utility\transform;

/** @internal */
trait MapsEnterpriseSummaries
{
    use MapsAddresses;
    use MapsDescriptions;
    use MapsPeriods;
    use MapsStatuses;

    private function toEnterpriseSummary(Element $enterprise): EnterpriseSummary
    {
        $status = $enterprise->optional('Status') ?? $enterprise->optional('JuridicalSituation')?->optional('Status');

        return new EnterpriseSummary(
            number: EnterpriseNumber::fromString($enterprise->textOf('Number')),
            status: transform($status, $this->toStatus(...)),
            period: $this->toRequiredPeriod($enterprise, 'Period'),
            typeOfEnterprise: TypeOfEnterprise::from($enterprise->textOf('TypeOfEnterprise')),
            juridicalForm: transform($enterprise->optional('JuridicalForm'), fn (Element $form) => new JuridicalForm(
                $form->textOf('Code'),
                $this->toDescription($form),
                $this->toOptionalPeriod($form, 'ValidityPeriod'),
                null,
            )),
            primaryDenomination: $this->toDescription($enterprise, 'PrimaryDenomination'),
            address: transform($enterprise->optional('Address'), $this->toAddress(...)),
            businessUnits: (int) ($enterprise->optionalText('BusinessUnits') ?? 0),
        );
    }

    private function toEnterpriseSummaries(Element $parent): EnterpriseSummaries
    {
        return EnterpriseSummaries::of(array_map($this->toEnterpriseSummary(...), $parent->children('Enterprise')));
    }
}
