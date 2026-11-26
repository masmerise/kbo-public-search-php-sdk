<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Establishment;

use KboPublicSearch\Core\Type\Denomination\DenominationCode;
use KboPublicSearch\Core\Type\ExOfficioStrikingOff\ExOfficioStrikingOffCode;
use KboPublicSearch\Core\Type\Status\StatusCode;
use KboPublicSearch\Core\Xml\Reader;
use KboPublicSearch\Establishment\Type\Mapping\EstablishmentMapper;
use KboPublicSearch\Tests\RecordedTestCase;
use KboPublicSearch\Tests\Support\Recording;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class EstablishmentMapperTest extends TestCase
{
    #[Test]
    public function recorded_short(): void
    {
        // Act
        $establishment = $this->establishmentMapper()->toEstablishment(Recording::unwrapped('establishment/find-short')->body->child('Establishment'));

        // Assert
        $this->assertSame(RecordedTestCase::ESTABLISHMENT, $establishment->establishmentNumber->toString());
        $this->assertSame(StatusCode::Active, $establishment->status->code);
        $this->assertSame(RecordedTestCase::ENTERPRISE, $establishment->enterpriseNumber?->toString());
        $this->assertSame(StatusCode::Active, $establishment->enterpriseStatus?->code);
        $this->assertSame('REDACTED', $establishment->primaryDenomination->first()); // establishment names are redacted in fixtures
        $this->assertSame(0, $establishment->denominations->count());
        $this->assertSame(0, $establishment->activities->count());
        $this->assertSame(0, $establishment->authorizations->count());
        $this->assertSame(0, $establishment->enterpriseExOfficioStrikingOffs->count());
        $this->assertSame('9000', $establishment->address?->zipcode);
        $this->assertSame('44021', $establishment->address?->municipality->code?->toString());
    }

    #[Test]
    public function recorded_long(): void
    {
        // Act
        $establishment = $this->establishmentMapper()->toEstablishment(Recording::unwrapped('establishment/find-long')->body->child('Establishment'));

        // Assert
        $this->assertSame([], $establishment->primaryDenomination->all());
        $this->assertSame(2, $establishment->denominations->count());
        $this->assertSame(DenominationCode::TradeName, $establishment->denominations->first()?->code);
        $this->assertGreaterThan(0, $establishment->activities->count());
        $this->assertNull($establishment->address?->typeOfAddress);
    }

    #[Test]
    public function without_enterprise(): void
    {
        // Arrange (ReadEstablishmentByEstablishmentNumber may exceptionally omit EnterpriseNumber/EnterpriseStatus, cookbook §2.6.2)
        $xml = <<<'XML'
        <R xmlns:ns2="http://economie.fgov.be/kbopub/webservices/v1/datamodel"><ns2:Establishment>
          <ns2:EstablishmentNumber>2147241191</ns2:EstablishmentNumber>
          <ns2:Status><ns2:Code>ST</ns2:Code><ns2:Description><ns2:Value>Stopgezet</ns2:Value><ns2:Language>nl</ns2:Language></ns2:Description></ns2:Status>
          <ns2:Period><ns2:Begin>2002-03-05</ns2:Begin><ns2:End>2010-01-01</ns2:End></ns2:Period>
          <ns2:EnterpriseExOfficioStrikingOff><ns2:Code>004</ns2:Code><ns2:Description><ns2:Value>niet-neerlegging jaarrekening</ns2:Value><ns2:Language>nl</ns2:Language></ns2:Description></ns2:EnterpriseExOfficioStrikingOff>
          <ns2:PrimaryDenomination><ns2:Value>Zuiderpoort Office Park</ns2:Value><ns2:Language>nl</ns2:Language></ns2:PrimaryDenomination>
          <ns2:Address>
            <ns2:Street><ns2:Code>1234</ns2:Code><ns2:Description><ns2:Value>Gaston Crommenlaan</ns2:Value><ns2:Language>nl</ns2:Language></ns2:Description></ns2:Street>
            <ns2:HouseNumber>4</ns2:HouseNumber><ns2:Zipcode>9050</ns2:Zipcode>
            <ns2:Municipality><ns2:Code>44021</ns2:Code><ns2:Description><ns2:Value>Gent</ns2:Value><ns2:Language>nl</ns2:Language></ns2:Description></ns2:Municipality>
          </ns2:Address>
        </ns2:Establishment></R>
        XML;

        // Act
        $establishment = $this->establishmentMapper()->toEstablishments(Reader::fromString($xml))->first();

        // Assert
        $this->assertNull($establishment?->enterpriseNumber);
        $this->assertNull($establishment?->enterpriseStatus);
        $this->assertSame('2010-01-01', $establishment?->period->end?->toString());
        $this->assertSame(ExOfficioStrikingOffCode::StrikingOffAsAResultOfNonFilingOfAnnualAccounts, $establishment?->enterpriseExOfficioStrikingOffs->first()?->code);
        $this->assertSame('Zuiderpoort Office Park', (string) $establishment?->primaryDenomination);
    }

    private function establishmentMapper(): EstablishmentMapper
    {
        return new EstablishmentMapper();
    }
}
