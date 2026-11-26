<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Enterprise;

use KboPublicSearch\Core\Http\Soap\Envelope;
use KboPublicSearch\Core\Type\ExOfficioStrikingOff\ExOfficioStrikingOffCode;
use KboPublicSearch\Core\Type\Status\StatusCode;
use KboPublicSearch\Core\Type\TypeOfEnterprise;
use KboPublicSearch\Enterprise\Type\Currency;
use KboPublicSearch\Enterprise\Type\Enterprise;
use KboPublicSearch\Enterprise\Type\EventCode;
use KboPublicSearch\Enterprise\Type\FunctionCode;
use KboPublicSearch\Enterprise\Type\JuridicalFormCode;
use KboPublicSearch\Enterprise\Type\JuridicalSituationCode;
use KboPublicSearch\Enterprise\Type\Mapping\EnterpriseMapper;
use KboPublicSearch\Localization\Language;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class EnterpriseMapperTest extends TestCase
{
    #[Test]
    public function maps_cookbook_example(): void
    {
        // Act
        $enterprise = $this->enterprise('reply-read-enterprise');

        // Assert
        $this->assertSame('0314595348', $enterprise->number->toString());
        $this->assertSame(JuridicalSituationCode::NormalSituation, $enterprise->juridicalSituation->code);
        $this->assertSame(StatusCode::Active, $enterprise->juridicalSituation->status->code);
        $this->assertSame('1968-01-01', $enterprise->juridicalSituation->validityPeriod?->begin?->toString());
        $this->assertNull($enterprise->juridicalSituation->event);
        $this->assertSame('1968-01-01', $enterprise->period->begin?->toString());
        $this->assertNull($enterprise->duration);
        $this->assertSame(TypeOfEnterprise::LegalPerson, $enterprise->typeOfEnterprise);
        $this->assertSame(JuridicalFormCode::FederalPublicService, $enterprise->juridicalForm?->code);
        $this->assertNull($enterprise->juridicalForm?->juridicalFormCac);
        $this->assertSame(6, $enterprise->denominations->count());
        $this->assertSame('Federale Overheidsdienst Economie, KMO, Middenstand en Energie', $enterprise->denominations->primary(Language::Dutch)?->name);
        $this->assertSame('1210', $enterprise->addresses->first()?->zipcode);
        $this->assertSame(4, $enterprise->contactInformation->count());
        $this->assertSame(17, $enterprise->businessUnits);
        $this->assertSame(0, $enterprise->branches->count());
        $this->assertSame(1, $enterprise->professionalCompetences->count());
        $this->assertSame(3, $enterprise->qualifications->count());
        $this->assertSame(1, $enterprise->authorizations->count());
        $this->assertSame(6, $enterprise->activities->count());
        $this->assertSame(13, $enterprise->functions->count());
        $this->assertSame(FunctionCode::Director, $enterprise->functions->first()?->code);
        $this->assertSame('John', $enterprise->functions->first()?->person?->givenName);
        $this->assertSame('Doe', $enterprise->functions->first()?->person?->surname);
        $this->assertSame('0354317739', $enterprise->linkedEnterprises->first()?->enterpriseNumberSubject->toString());
        $this->assertNull($enterprise->capital);
        $this->assertNull($enterprise->financialData);
        $this->assertSame(0, $enterprise->replacements->count());
        $this->assertSame(0, $enterprise->exOfficioStrikingOffs->count());
    }

    #[Test]
    public function maps_optional_sections(): void
    {
        // Act
        $enterprise = $this->enterprise('reply-read-enterprise-optional');

        // Assert
        $this->assertSame('2011-03-23', $enterprise->period->end?->toString());
        $this->assertSame(30, $enterprise->duration);
        $this->assertSame(StatusCode::Stopped, $enterprise->juridicalSituation->status->code);
        $this->assertSame(EventCode::Rehabilitation, $enterprise->juridicalSituation->event?->code);
        $this->assertSame('2011-07-13', $enterprise->juridicalSituation->event?->eventDate->toString());
        $this->assertSame('1000000000.00', $enterprise->capital?->amount->toString());
        $this->assertSame(Currency::ECUFormerMonetaryUnitOfTheEC, $enterprise->capital?->currency);
        $this->assertSame('1966-05-26', $enterprise->juridicalForm?->validityPeriod?->begin?->toString());
        $this->assertSame(JuridicalFormCode::PrivateLimitedCompany, $enterprise->juridicalForm?->juridicalFormCac?->code);
        $this->assertSame('2020-01-01', $enterprise->juridicalForm?->juridicalFormCac?->cacDate->toString());

        $branch = $enterprise->branches->first();
        $this->assertSame('9001975528', $branch?->id->toString());
        $this->assertSame(StatusCode::Active, $branch?->status->code);
        $this->assertSame('1050', $branch?->address?->zipcode);
        $this->assertSame('Verspringt', $branch?->denominations->first()?->name);
        $this->assertSame('2002-03-03', $branch?->validityPeriod->begin?->toString());

        $this->assertSame(9, $enterprise->financialData?->annualMeetingMonth);
        $this->assertSame(31, $enterprise->financialData?->fiscalYearEndDay);
        $this->assertSame(3, $enterprise->financialData?->fiscalYearEndMonth);
        $this->assertSame('2014-03-31', $enterprise->financialData?->exceptionalFiscalYear?->end?->toString());
        $this->assertSame('2013-09-24', $enterprise->financialData?->validityPeriod?->begin?->toString());

        $syndic = $enterprise->functions[0];
        $this->assertSame('0788006323', $syndic->professionalSyndic?->toString());
        $this->assertSame('2025-08-01', $syndic->strikingOff?->toString());
        $this->assertNull($syndic->enterpriseNumber);
        $this->assertNull($syndic->functionCac);
        $this->assertNull($syndic->activeFlag);

        $manager = $enterprise->functions[1];
        $this->assertSame(FunctionCode::Director, $manager->functionCac?->code);
        $this->assertSame('2020-01-01', $manager->functionCac?->cacPeriod->begin?->toString());
        $this->assertTrue($manager->activeFlag);

        $competence = $enterprise->professionalCompetences->first();
        $this->assertSame('2008-11-04', $competence?->exemption?->begin?->toString());
        $this->assertSame('2025-08-01', $competence?->strikingOff?->toString());

        $this->assertSame('0406572926', $enterprise->replacements->first()?->enterpriseNumber->toString());
        $this->assertSame('2008-12-23', $enterprise->replacements->first()?->period->begin?->toString());
        $this->assertSame(ExOfficioStrikingOffCode::StrikingOffBasedOnAJudgment, $enterprise->exOfficioStrikingOffs->first()?->code);
    }

    #[Test]
    public function function_held_by_an_enterprise(): void
    {
        // Act
        $function = $this->enterprise('reply-read-enterprise-optional')->functions[2];

        // Assert
        $this->assertSame('0447757344', $function->enterpriseNumber?->toString());
        $this->assertSame('John', $function->person?->givenName);
    }

    private function enterprise(string $fixture): Enterprise
    {
        $unwrapped = Envelope::unwrap(file_get_contents(__DIR__ . "/../../Fixtures/Cookbook/{$fixture}.xml"));

        return $this->enterpriseMapper()->toEnterprise($unwrapped->body->child('Enterprise'));
    }

    private function enterpriseMapper(): EnterpriseMapper
    {
        return new EnterpriseMapper();
    }
}
