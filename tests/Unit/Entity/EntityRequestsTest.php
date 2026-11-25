<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Entity;

use InvalidArgumentException;
use KboPublicSearch\Core\Type\Parameter\LocationFilter;
use KboPublicSearch\Core\Type\Primitive\HouseNumber;
use KboPublicSearch\Core\Type\Primitive\StreetName;
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\Entity\SearchByActivity\Request\ReadEnterpriseOrEstablishmentByActivityRequest;
use KboPublicSearch\Entity\SearchByAddress\Request\ReadEnterpriseOrEstablishmentByAddressRequest;
use KboPublicSearch\Entity\Type\ActivityFilter;
use KboPublicSearch\Entity\Type\EntityFilter;
use KboPublicSearch\Entity\Type\Primitive\ActivityCode;
use KboPublicSearch\Tests\Support\Message;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class EntityRequestsTest extends TestCase
{
    #[Test]
    public function address_request(): void
    {
        // Arrange
        $request = new ReadEnterpriseOrEstablishmentByAddressRequest(Zipcode::fromString('1210'), StreetName::fromString('Vooruitgangsstraat'), HouseNumber::fromString('50'), true);

        // Assert
        $this->assertSame('ReadEnterpriseOrEstablishmentByAddress', $request->operation());
        $this->assertSame(['Zipcode', 'StreetName', 'HouseNumber', 'ActiveFilter'], Message::names($request));
        $this->assertStringContainsString('<mes:Zipcode>1210</mes:Zipcode><mes:StreetName>Vooruitgangsstraat</mes:StreetName><mes:HouseNumber>50</mes:HouseNumber><mes:ActiveFilter>true</mes:ActiveFilter>', Message::xml($request));
    }

    #[Test]
    public function activity_request(): void
    {
        // Arrange
        $request = new ReadEnterpriseOrEstablishmentByActivityRequest(ActivityFilter::of('10711', '4711'), LocationFilter::zipcodes('9300'), EntityFilter::legalPersons()->with(EntityFilter::establishments()), false);

        // Act
        $xml = Message::xml($request);

        // Assert
        $this->assertSame('ReadEnterpriseOrEstablishmentByActivity', $request->operation());
        $this->assertSame(['ActivityFilter', 'LocationFilter', 'EnterprisePhysicalPersonFilter', 'EnterpriseLegalPersonFilter', 'EstablishmentFilter', 'ActiveFilter'], Message::names($request));
        $this->assertStringContainsString('<mes:ActivityFilter><mes:ActivityCode>10711</mes:ActivityCode><mes:ActivityCode>4711</mes:ActivityCode></mes:ActivityFilter>', $xml);
        $this->assertStringContainsString('<mes:EnterprisePhysicalPersonFilter>false</mes:EnterprisePhysicalPersonFilter><mes:EnterpriseLegalPersonFilter>true</mes:EnterpriseLegalPersonFilter><mes:EstablishmentFilter>true</mes:EstablishmentFilter><mes:ActiveFilter>false</mes:ActiveFilter>', $xml);
    }

    #[Test]
    public function activity_codes_are_validated(): void
    {
        foreach ([fn () => ActivityFilter::of(), fn () => ActivityCode::fromString('41'), fn () => ActivityCode::fromString('12345678'), fn () => ActivityCode::fromString('10a11'), fn () => ActivityFilter::of(...array_fill(0, 11, '10711'))] as $build) {
            try {
                $build();
                $this->fail('Expected InvalidArgumentException.');
            } catch (InvalidArgumentException) {
                $this->addToAssertionCount(1);
            }
        }
    }
}
