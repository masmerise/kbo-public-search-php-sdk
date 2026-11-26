<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type\Mapping;

use KboPublicSearch\Core\Type\Mapping\ActivityMapper;
use KboPublicSearch\Core\Type\Mapping\AddressMapper;
use KboPublicSearch\Core\Type\Mapping\AuthorizationMapper;
use KboPublicSearch\Core\Type\Mapping\ContactInformationMapper;
use KboPublicSearch\Core\Type\Mapping\DenominationMapper;
use KboPublicSearch\Core\Type\Mapping\DescriptionMapper;
use KboPublicSearch\Core\Type\Mapping\ExOfficioStrikingOffMapper;
use KboPublicSearch\Core\Type\Mapping\PeriodMapper;
use KboPublicSearch\Core\Type\Mapping\StatusMapper;
use KboPublicSearch\Core\Type\Primitive\Date;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Core\Type\TypeOfEnterprise;
use KboPublicSearch\Core\Xml\Element;
use KboPublicSearch\Enterprise\Type\Branch;
use KboPublicSearch\Enterprise\Type\Branches;
use KboPublicSearch\Enterprise\Type\Capital;
use KboPublicSearch\Enterprise\Type\Currency;
use KboPublicSearch\Enterprise\Type\Enterprise;
use KboPublicSearch\Enterprise\Type\EnterpriseFunction;
use KboPublicSearch\Enterprise\Type\Event;
use KboPublicSearch\Enterprise\Type\EventCode;
use KboPublicSearch\Enterprise\Type\FinancialData;
use KboPublicSearch\Enterprise\Type\FunctionCac;
use KboPublicSearch\Enterprise\Type\FunctionCode;
use KboPublicSearch\Enterprise\Type\Functions;
use KboPublicSearch\Enterprise\Type\JuridicalForm;
use KboPublicSearch\Enterprise\Type\JuridicalFormCac;
use KboPublicSearch\Enterprise\Type\JuridicalFormCode;
use KboPublicSearch\Enterprise\Type\JuridicalSituation;
use KboPublicSearch\Enterprise\Type\JuridicalSituationCode;
use KboPublicSearch\Enterprise\Type\LinkedEnterprise;
use KboPublicSearch\Enterprise\Type\LinkedEnterpriseCode;
use KboPublicSearch\Enterprise\Type\LinkedEnterprises;
use KboPublicSearch\Enterprise\Type\Person;
use KboPublicSearch\Enterprise\Type\Primitive\Amount;
use KboPublicSearch\Enterprise\Type\Primitive\BranchId;
use KboPublicSearch\Enterprise\Type\Primitive\QualificationCode;
use KboPublicSearch\Enterprise\Type\ProfessionalCompetence;
use KboPublicSearch\Enterprise\Type\ProfessionalCompetenceCode;
use KboPublicSearch\Enterprise\Type\ProfessionalCompetences;
use KboPublicSearch\Enterprise\Type\Qualification;
use KboPublicSearch\Enterprise\Type\Qualifications;
use KboPublicSearch\Enterprise\Type\Replacement;
use KboPublicSearch\Enterprise\Type\Replacements;

use function KboPublicSearch\Core\Utility\transform;

/** @internal */
final readonly class EnterpriseMapper
{
    public function __construct(
        private PeriodMapper $periodMapper = new PeriodMapper(),
        private DenominationMapper $denominationMapper = new DenominationMapper(),
        private AddressMapper $addressMapper = new AddressMapper(),
        private ContactInformationMapper $contactInformationMapper = new ContactInformationMapper(),
        private AuthorizationMapper $authorizationMapper = new AuthorizationMapper(),
        private ActivityMapper $activityMapper = new ActivityMapper(),
        private ExOfficioStrikingOffMapper $exOfficioStrikingOffMapper = new ExOfficioStrikingOffMapper(),
        private StatusMapper $statusMapper = new StatusMapper(),
        private DescriptionMapper $descriptionMapper = new DescriptionMapper(),
    ) {}

    public function toEnterprise(Element $enterprise): Enterprise
    {
        return new Enterprise(
            number: EnterpriseNumber::fromString($enterprise->textOf('Number')),
            juridicalSituation: $this->toJuridicalSituation($enterprise->child('JuridicalSituation')),
            period: $this->periodMapper->toRequiredPeriod($enterprise, 'Period'),
            duration: transform($enterprise->optionalText('Duration'), intval(...)),
            typeOfEnterprise: TypeOfEnterprise::from($enterprise->textOf('TypeOfEnterprise')),
            juridicalForm: transform($enterprise->optional('JuridicalForm'), $this->toJuridicalForm(...)),
            capital: transform($enterprise->optional('Capital'), static fn (Element $capital) => new Capital(
                new Amount($capital->textOf('Amount')),
                transform($capital->optionalText('Currency'), Currency::from(...)),
            )),
            denominations: $this->denominationMapper->toDenominations($enterprise),
            addresses: $this->addressMapper->toAddresses($enterprise),
            contactInformation: $this->contactInformationMapper->toContactInformations($enterprise),
            businessUnits: (int) ($enterprise->optionalText('BusinessUnits') ?? 0),
            branches: Branches::of(array_map($this->toBranch(...), $enterprise->children('Branch'))),
            professionalCompetences: ProfessionalCompetences::of(array_map($this->toProfessionalCompetence(...), $enterprise->children('ProfessionalCompetence'))),
            qualifications: Qualifications::of(array_map($this->toQualification(...), $enterprise->children('Qualification'))),
            authorizations: $this->authorizationMapper->toAuthorizations($enterprise),
            activities: $this->activityMapper->toActivities($enterprise),
            functions: Functions::of(array_map($this->toFunction(...), $enterprise->children('Function'))),
            linkedEnterprises: LinkedEnterprises::of(array_map($this->toLinkedEnterprise(...), $enterprise->children('LinkedEnterprise'))),
            financialData: transform($enterprise->optional('FinancialData'), $this->toFinancialData(...)),
            replacements: Replacements::of(array_map($this->toReplacement(...), $enterprise->children('Replacement'))),
            exOfficioStrikingOffs: $this->exOfficioStrikingOffMapper->toExOfficioStrikingOffs($enterprise),
        );
    }

    private function toBranch(Element $branch): Branch
    {
        return new Branch(
            id: new BranchId($branch->textOf('Id')),
            status: $this->statusMapper->toStatus($branch->child('Status')),
            address: transform($branch->optional('Address'), $this->addressMapper->toAddress(...)),
            denominations: $this->denominationMapper->toDenominations($branch),
            validityPeriod: $this->periodMapper->toRequiredPeriod($branch, 'ValidityPeriod'),
        );
    }

    private function toFinancialData(Element $data): FinancialData
    {
        return new FinancialData(
            annualMeetingMonth: transform($data->optionalText('AnnualMeetingMonth'), intval(...)),
            fiscalYearEndDay: transform($data->optionalText('FiscalYearEndDay'), intval(...)),
            fiscalYearEndMonth: transform($data->optionalText('FiscalYearEndMonth'), intval(...)),
            exceptionalFiscalYear: $this->periodMapper->toOptionalPeriod($data, 'ExceptionalFiscalYear'),
            validityPeriod: $this->periodMapper->toOptionalPeriod($data, 'ValidityPeriod'),
        );
    }

    private function toFunction(Element $function): EnterpriseFunction
    {
        return new EnterpriseFunction(
            code: FunctionCode::from($function->textOf('Code')),
            description: $this->descriptionMapper->toDescription($function),
            period: $this->periodMapper->toRequiredPeriod($function, 'Period'),
            person: transform($function->optional('Person'), static fn (Element $person) => new Person(
                $person->optionalText('Surname') ?? '',
                $person->optionalText('GivenName') ?? '',
            )),
            enterpriseNumber: transform($function->optionalText('EnterpriseNumber'), EnterpriseNumber::fromString(...)),
            professionalSyndic: transform($function->optional('ProfessionalSyndic')?->optionalText('EnterpriseNumber'), EnterpriseNumber::fromString(...)),
            functionCac: transform($function->optional('FunctionCAC'), fn (Element $cac) => new FunctionCac(
                FunctionCode::from($cac->textOf('Code')),
                $this->descriptionMapper->toDescription($cac),
                $this->periodMapper->toRequiredPeriod($cac, 'CACPeriod'),
            )),
            strikingOff: $this->periodMapper->toDate($function, 'StrikingOff'),
            activeFlag: transform($function->optionalText('ActiveFlag'), static fn (string $flag) => $flag === 'true'),
        );
    }

    private function toJuridicalForm(Element $form): JuridicalForm
    {
        return new JuridicalForm(
            code: JuridicalFormCode::from($form->textOf('Code')),
            description: $this->descriptionMapper->toDescription($form),
            validityPeriod: $this->periodMapper->toOptionalPeriod($form, 'ValidityPeriod'),
            juridicalFormCac: transform($form->optional('JuridicalFormCAC'), fn (Element $cac) => new JuridicalFormCac(
                JuridicalFormCode::from($cac->textOf('Code')),
                $this->descriptionMapper->toDescription($cac),
                Date::fromString($cac->textOf('CACDate')),
            )),
        );
    }

    private function toJuridicalSituation(Element $situation): JuridicalSituation
    {
        return new JuridicalSituation(
            code: transform($situation->optionalText('Code'), JuridicalSituationCode::from(...)),
            description: $this->descriptionMapper->toDescription($situation),
            validityPeriod: $this->periodMapper->toOptionalPeriod($situation, 'ValidityPeriod'),
            status: $this->statusMapper->toStatus($situation->child('Status')),
            event: transform($situation->optional('Event'), fn (Element $event) => new Event(
                EventCode::from($event->textOf('Code')),
                $this->descriptionMapper->toDescription($event),
                Date::fromString($event->textOf('EventDate')),
            )),
        );
    }

    private function toLinkedEnterprise(Element $link): LinkedEnterprise
    {
        return new LinkedEnterprise(
            code: LinkedEnterpriseCode::from($link->textOf('Code')),
            description: $this->descriptionMapper->toDescription($link),
            enterpriseNumberSubject: EnterpriseNumber::fromString($link->textOf('EnterpriseNumberSubject')),
            enterpriseNumberObject: EnterpriseNumber::fromString($link->textOf('EnterpriseNumberObject')),
            period: $this->periodMapper->toRequiredPeriod($link, 'Period'),
        );
    }

    private function toProfessionalCompetence(Element $competence): ProfessionalCompetence
    {
        return new ProfessionalCompetence(
            code: ProfessionalCompetenceCode::from($competence->textOf('Code')),
            description: $this->descriptionMapper->toDescription($competence),
            period: $this->periodMapper->toRequiredPeriod($competence, 'Period'),
            exemption: $this->periodMapper->toOptionalPeriod($competence, 'Exemption'),
            strikingOff: $this->periodMapper->toDate($competence, 'StrikingOff'),
        );
    }

    private function toQualification(Element $qualification): Qualification
    {
        return new Qualification(
            code: QualificationCode::fromString($qualification->textOf('Code')),
            description: $this->descriptionMapper->toDescription($qualification),
            period: $this->periodMapper->toRequiredPeriod($qualification, 'Period'),
        );
    }

    private function toReplacement(Element $replacement): Replacement
    {
        return new Replacement(
            enterpriseNumber: EnterpriseNumber::fromString($replacement->textOf('EnterpriseNumber')),
            period: $this->periodMapper->toRequiredPeriod($replacement, 'Period'),
        );
    }
}
