<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Entity;

use KboPublicSearch\Core\Xml\Reader;
use KboPublicSearch\Enterprise\Type\EnterpriseSummary;
use KboPublicSearch\Entity\Type\Mapping\MapsEntities;
use KboPublicSearch\Establishment\Type\Establishment;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class MapsEntitiesTest extends TestCase
{
    use MapsEntities;

    #[Test]
    public function keeps_the_reply_order(): void
    {
        // Arrange (cookbook §2.9.2: enterprises and establishments mixed)
        $enterprise = static fn (string $number) => "<ns2:Enterprise><ns2:Number>{$number}</ns2:Number>"
            . '<ns2:JuridicalSituation><ns2:Status><ns2:Code>AC</ns2:Code></ns2:Status></ns2:JuridicalSituation>'
            . '<ns2:Period><ns2:Begin>1968-01-01</ns2:Begin></ns2:Period><ns2:TypeOfEnterprise>ELP</ns2:TypeOfEnterprise>'
            . '<ns2:PrimaryDenomination><ns2:Value>FOD Economie</ns2:Value><ns2:Language>nl</ns2:Language></ns2:PrimaryDenomination>'
            . '<ns2:BusinessUnits>1</ns2:BusinessUnits></ns2:Enterprise>';
        $xml = '<ReadEnterpriseOrEstablishmentByAddressReply xmlns:ns2="http://economie.fgov.be/kbopub/webservices/v1/datamodel" xmlns="http://economie.fgov.be/kbopub/webservices/v1/messages">'
            . $enterprise('314595348')
            . '<ns2:Establishment><ns2:EstablishmentNumber>2147241191</ns2:EstablishmentNumber><ns2:Status><ns2:Code>AC</ns2:Code></ns2:Status>'
            . '<ns2:Period><ns2:Begin>2002-03-05</ns2:Begin></ns2:Period><ns2:EnterpriseNumber>314595348</ns2:EnterpriseNumber></ns2:Establishment>'
            . $enterprise('1037799832')
            . '</ReadEnterpriseOrEstablishmentByAddressReply>';

        // Act
        $entities = $this->toEntities(Reader::fromString($xml));

        // Assert
        $this->assertCount(3, $entities);
        $this->assertInstanceOf(EnterpriseSummary::class, $entities[0]);
        $this->assertInstanceOf(Establishment::class, $entities[1]);
        $this->assertInstanceOf(EnterpriseSummary::class, $entities[2]);
        $this->assertCount(2, $entities->enterprises());
        $this->assertSame('1037799832', $entities->enterprises()[1]->number->toString());
        $this->assertCount(1, $entities->establishments());
        $this->assertSame('2147241191', $entities->establishments()->first()?->establishmentNumber->toString());
    }
}
