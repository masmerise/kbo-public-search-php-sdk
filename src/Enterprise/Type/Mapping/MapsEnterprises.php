<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type\Mapping;

use KboPublicSearch\Core\Type\Mapping\MapsActivities;
use KboPublicSearch\Core\Type\Mapping\MapsAddresses;
use KboPublicSearch\Core\Type\Mapping\MapsAuthorizations;
use KboPublicSearch\Core\Type\Mapping\MapsContactInformation;
use KboPublicSearch\Core\Type\Mapping\MapsDenominations;
use KboPublicSearch\Core\Type\Mapping\MapsDescriptions;
use KboPublicSearch\Core\Type\Mapping\MapsExOfficioStrikingOffs;
use KboPublicSearch\Core\Type\Mapping\MapsPeriods;
use KboPublicSearch\Core\Type\Mapping\MapsStatuses;
use KboPublicSearch\Core\Type\Primitive\Date;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Core\Xml\Element;
use KboPublicSearch\Enterprise\Type\Branch;
use KboPublicSearch\Enterprise\Type\Branches;
use KboPublicSearch\Enterprise\Type\Capital;
use KboPublicSearch\Enterprise\Type\Enterprise;
use KboPublicSearch\Enterprise\Type\EnterpriseFunction;
use KboPublicSearch\Enterprise\Type\Event;
use KboPublicSearch\Enterprise\Type\FinancialData;
use KboPublicSearch\Enterprise\Type\FunctionCac;
use KboPublicSearch\Enterprise\Type\Functions;
use KboPublicSearch\Enterprise\Type\JuridicalForm;
use KboPublicSearch\Enterprise\Type\JuridicalFormCac;
use KboPublicSearch\Enterprise\Type\JuridicalSituation;
use KboPublicSearch\Enterprise\Type\LinkedEnterprise;
use KboPublicSearch\Enterprise\Type\LinkedEnterprises;
use KboPublicSearch\Enterprise\Type\Person;
use KboPublicSearch\Enterprise\Type\ProfessionalCompetence;
use KboPublicSearch\Enterprise\Type\ProfessionalCompetences;
use KboPublicSearch\Enterprise\Type\Qualification;
use KboPublicSearch\Enterprise\Type\Qualifications;
use KboPublicSearch\Enterprise\Type\Replacement;
use KboPublicSearch\Enterprise\Type\Replacements;
use KboPublicSearch\Enterprise\Type\TypeOfEnterprise;

use function KboPublicSearch\Core\Utility\transform;

/** @internal */
trait MapsEnterprises
{
    use MapsActivities;
    use MapsAddresses;
    use MapsAuthorizations;
    use MapsContactInformation;
    use MapsDenominations;
    use MapsExOfficioStrikingOffs;
    use MapsDescriptions;
    use MapsPeriods;
    use MapsStatuses;

    private function toEnterprise(Element $enterprise): Enterprise
    {
        return new Enterprise(
            number: EnterpriseNumber::fromString($enterprise->textOf('Number')),
            juridicalSituation: $this->toJuridicalSituation($enterprise->child('JuridicalSituation')),
            period: $this->toRequiredPeriod($enterprise, 'Period'),
            duration: transform($enterprise->optionalText('Duration'), intval(...)),
            typeOfEnterprise: TypeOfEnterprise::from($enterprise->textOf('TypeOfEnterprise')),
            juridicalForm: transform($enterprise->optional('JuridicalForm'), $this->toJuridicalForm(...)),
            capital: transform($enterprise->optional('Capital'), static fn (Element $capital) => new Capital(
                $capital->textOf('Amount'),
                $capital->optionalText('Currency') ?? '',
            )),
            denominations: $this->toDenominations($enterprise),
            addresses: $this->toAddresses($enterprise),
            contactInformation: $this->toContactInformations($enterprise),
            businessUnits: (int) ($enterprise->optionalText('BusinessUnits') ?? 0),
            branches: Branches::of(array_map($this->toBranch(...), $enterprise->children('Branch'))),
            professionalCompetences: ProfessionalCompetences::of(array_map($this->toProfessionalCompetence(...), $enterprise->children('ProfessionalCompetence'))),
            qualifications: Qualifications::of(array_map($this->toQualification(...), $enterprise->children('Qualification'))),
            authorizations: $this->toAuthorizations($enterprise),
            activities: $this->toActivities($enterprise),
            functions: Functions::of(array_map($this->toFunction(...), $enterprise->children('Function'))),
            linkedEnterprises: LinkedEnterprises::of(array_map($this->toLinkedEnterprise(...), $enterprise->children('LinkedEnterprise'))),
            financialData: transform($enterprise->optional('FinancialData'), $this->toFinancialData(...)),
            replacements: Replacements::of(array_map($this->toReplacement(...), $enterprise->children('Replacement'))),
            exOfficioStrikingOffs: $this->toExOfficioStrikingOffs($enterprise),
        );
    }

    private function toBranch(Element $branch): Branch
    {
        return new Branch(
            id: $branch->textOf('Id'),
            status: $this->toStatus($branch->child('Status')),
            address: transform($branch->optional('Address'), $this->toAddress(...)),
            denominations: $this->toDenominations($branch),
            validityPeriod: $this->toRequiredPeriod($branch, 'ValidityPeriod'),
        );
    }

    private function toFinancialData(Element $data): FinancialData
    {
        return new FinancialData(
            annualMeetingMonth: transform($data->optionalText('AnnualMeetingMonth'), intval(...)),
            fiscalYearEndDay: transform($data->optionalText('FiscalYearEndDay'), intval(...)),
            fiscalYearEndMonth: transform($data->optionalText('FiscalYearEndMonth'), intval(...)),
            exceptionalFiscalYear: $this->toOptionalPeriod($data, 'ExceptionalFiscalYear'),
            validityPeriod: $this->toOptionalPeriod($data, 'ValidityPeriod'),
        );
    }

    private function toFunction(Element $function): EnterpriseFunction
    {
        return new EnterpriseFunction(
            code: $function->textOf('Code'),
            description: $this->toDescription($function),
            period: $this->toRequiredPeriod($function, 'Period'),
            person: transform($function->optional('Person'), static fn (Element $person) => new Person(
                $person->optionalText('Surname') ?? '',
                $person->optionalText('GivenName') ?? '',
            )),
            enterpriseNumber: transform($function->optionalText('EnterpriseNumber'), EnterpriseNumber::fromString(...)),
            professionalSyndic: transform($function->optional('ProfessionalSyndic')?->optionalText('EnterpriseNumber'), EnterpriseNumber::fromString(...)),
            functionCac: transform($function->optional('FunctionCAC'), fn (Element $cac) => new FunctionCac(
                $cac->textOf('Code'),
                $this->toDescription($cac),
                $this->toRequiredPeriod($cac, 'CACPeriod'),
            )),
            strikingOff: $this->toDate($function, 'StrikingOff'),
            activeFlag: transform($function->optionalText('ActiveFlag'), static fn (string $flag) => $flag === 'true'),
        );
    }

    private function toJuridicalForm(Element $form): JuridicalForm
    {
        return new JuridicalForm(
            code: $form->textOf('Code'),
            description: $this->toDescription($form),
            validityPeriod: $this->toOptionalPeriod($form, 'ValidityPeriod'),
            juridicalFormCac: transform($form->optional('JuridicalFormCAC'), fn (Element $cac) => new JuridicalFormCac(
                $cac->textOf('Code'),
                $this->toDescription($cac),
                Date::fromString($cac->textOf('CACDate')),
            )),
        );
    }

    private function toJuridicalSituation(Element $situation): JuridicalSituation
    {
        return new JuridicalSituation(
            code: $situation->optionalText('Code') ?? '',
            description: $this->toDescription($situation),
            validityPeriod: $this->toOptionalPeriod($situation, 'ValidityPeriod'),
            status: $this->toStatus($situation->child('Status')),
            event: transform($situation->optional('Event'), fn (Element $event) => new Event(
                $event->textOf('Code'),
                $this->toDescription($event),
                Date::fromString($event->textOf('EventDate')),
            )),
        );
    }

    private function toLinkedEnterprise(Element $link): LinkedEnterprise
    {
        return new LinkedEnterprise(
            code: $link->textOf('Code'),
            description: $this->toDescription($link),
            enterpriseNumberSubject: EnterpriseNumber::fromString($link->textOf('EnterpriseNumberSubject')),
            enterpriseNumberObject: EnterpriseNumber::fromString($link->textOf('EnterpriseNumberObject')),
            period: $this->toRequiredPeriod($link, 'Period'),
        );
    }

    private function toProfessionalCompetence(Element $competence): ProfessionalCompetence
    {
        return new ProfessionalCompetence(
            code: $competence->textOf('Code'),
            description: $this->toDescription($competence),
            period: $this->toRequiredPeriod($competence, 'Period'),
            exemption: $this->toOptionalPeriod($competence, 'Exemption'),
            strikingOff: $this->toDate($competence, 'StrikingOff'),
        );
    }

    private function toQualification(Element $qualification): Qualification
    {
        return new Qualification(
            code: $qualification->textOf('Code'),
            description: $this->toDescription($qualification),
            period: $this->toRequiredPeriod($qualification, 'Period'),
        );
    }

    private function toReplacement(Element $replacement): Replacement
    {
        return new Replacement(
            enterpriseNumber: EnterpriseNumber::fromString($replacement->textOf('EnterpriseNumber')),
            period: $this->toRequiredPeriod($replacement, 'Period'),
        );
    }
}
