<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Enterprise;

use KboPublicSearch\Core\Type\Shared\StatusKind;
use KboPublicSearch\Core\Xml\Reader;
use KboPublicSearch\Enterprise\Type\Mapping\MapsEnterpriseSummaries;
use KboPublicSearch\Enterprise\Type\TypeOfEnterprise;
use KboPublicSearch\Localization\Language;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class MapsEnterpriseSummariesTest extends TestCase
{
    use MapsEnterpriseSummaries;

    #[Test]
    public function maps_a_cookbook_search_result(): void
    {
        // Arrange (cookbook §2.2.2: Number, Status, Period, Type, JuridicalForm, PrimaryDenomination, Address, BusinessUnits)
        $xml = <<<'XML'
        <ReadEnterpriseByPhonemeReply xmlns:ns2="http://economie.fgov.be/kbopub/webservices/v1/datamodel" xmlns="http://economie.fgov.be/kbopub/webservices/v1/messages">
          <ns2:Enterprise>
            <ns2:Number>314595348</ns2:Number>
            <ns2:Status><ns2:Code>AC</ns2:Code><ns2:Description><ns2:Value>Actief</ns2:Value><ns2:Language>nl</ns2:Language></ns2:Description></ns2:Status>
            <ns2:Period><ns2:Begin>1968-01-01+01:00</ns2:Begin></ns2:Period>
            <ns2:TypeOfEnterprise>ELP</ns2:TypeOfEnterprise>
            <ns2:JuridicalForm><ns2:Code>301</ns2:Code><ns2:Description><ns2:Value>Federale overheidsdienst</ns2:Value><ns2:Language>nl</ns2:Language></ns2:Description></ns2:JuridicalForm>
            <ns2:PrimaryDenomination><ns2:Value>Federale Overheidsdienst Economie, KMO, Middenstand en Energie</ns2:Value><ns2:Language>nl</ns2:Language></ns2:PrimaryDenomination>
            <ns2:PrimaryDenomination><ns2:Value>Service public fédéral Economie, P.M.E., Classes moyennes et Energie</ns2:Value><ns2:Language>fr</ns2:Language></ns2:PrimaryDenomination>
            <ns2:BusinessUnits>17</ns2:BusinessUnits>
          </ns2:Enterprise>
        </ReadEnterpriseByPhonemeReply>
        XML;

        // Act
        $summaries = $this->toEnterpriseSummaries(Reader::fromString($xml));
        $summary = $summaries->first();

        // Assert
        $this->assertCount(1, $summaries);
        $this->assertSame('0314595348', $summary?->number->toString());
        $this->assertSame(StatusKind::Active, $summary?->status?->kind);
        $this->assertSame('1968-01-01', $summary?->period->begin?->toString());
        $this->assertSame(TypeOfEnterprise::LegalPerson, $summary?->type);
        $this->assertSame('301', $summary?->juridicalForm?->code);
        $this->assertSame('Service public fédéral Economie, P.M.E., Classes moyennes et Energie', $summary?->name->in(Language::French));
        $this->assertSame('Federale Overheidsdienst Economie, KMO, Middenstand en Energie', (string) $summary?->name);
        $this->assertNull($summary?->address);
        $this->assertSame(17, $summary?->businessUnits);
    }

    #[Test]
    public function status_falls_back_to_the_juridical_situation(): void
    {
        // Arrange (as production returns search results)
        $xml = <<<'XML'
        <R xmlns:ns2="urn:d"><ns2:Enterprise>
          <ns2:Number>206733130</ns2:Number>
          <ns2:JuridicalSituation><ns2:Code>000</ns2:Code><ns2:Status><ns2:Code>ST</ns2:Code><ns2:Description><ns2:Value>Stopgezet</ns2:Value><ns2:Language>nl</ns2:Language></ns2:Description></ns2:Status></ns2:JuridicalSituation>
          <ns2:Period><ns2:Begin>1969-07-31</ns2:Begin></ns2:Period>
          <ns2:TypeOfEnterprise>EPP</ns2:TypeOfEnterprise>
          <ns2:BusinessUnits>0</ns2:BusinessUnits>
        </ns2:Enterprise></R>
        XML;

        // Act
        $summary = $this->toEnterpriseSummaries(Reader::fromString($xml))->first();

        // Assert
        $this->assertSame(StatusKind::Stopped, $summary?->status?->kind);
        $this->assertNull($summary?->juridicalForm);
        $this->assertSame([], $summary?->name->all());
    }
}
