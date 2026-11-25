<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Type\Parameter;

use InvalidArgumentException;
use KboPublicSearch\Core\Type\Parameter\LocationFilter;
use KboPublicSearch\Core\Xml\Writer;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class LocationFilterTest extends TestCase
{
    private const array NS = ['mes' => 'http://economie.fgov.be/kbopub/webservices/v1/messages'];

    #[Test]
    public function zipcodes(): void
    {
        // Act
        $xml = Writer::write(LocationFilter::zipcodes('9300')->toNode(), self::NS);

        // Assert
        $this->assertStringContainsString('<mes:ZipcodeLocationFilter><mes:Zipcode>9300</mes:Zipcode></mes:ZipcodeLocationFilter></mes:LocationFilter>', $xml);
        $this->assertStringContainsString('<mes:LocationFilter xmlns:mes=', $xml);
    }

    #[Test]
    public function neighbouring_municipality(): void
    {
        // Act
        $xml = Writer::write(LocationFilter::neighbouringMunicipality('44021')->toNode(), self::NS);

        // Assert
        $this->assertStringContainsString('<mes:NeighboringMunicipalityLocationFilter><mes:MunicipalityCode>44021</mes:MunicipalityCode>', $xml);
    }

    #[Test]
    public function municipalities(): void
    {
        // Act
        $xml = Writer::write(LocationFilter::municipalities('44021', '21014')->toNode(), self::NS);

        // Assert
        $this->assertStringContainsString('<mes:MunicipalityLocationFilter><mes:MunicipalityCode>44021</mes:MunicipalityCode><mes:MunicipalityCode>21014</mes:MunicipalityCode></mes:MunicipalityLocationFilter>', $xml);
    }

    #[Test]
    public function no_zipcodes_throws(): void
    {
        $this->expectException(InvalidArgumentException::class);

        LocationFilter::zipcodes();
    }

    #[Test]
    public function five_zipcodes_throw(): void
    {
        $this->expectException(InvalidArgumentException::class);

        LocationFilter::zipcodes('9000', '9030', '9040', '9050', '9300');
    }

    #[Test]
    public function five_municipalities_throw(): void
    {
        $this->expectException(InvalidArgumentException::class);

        LocationFilter::municipalities('44021', '44021', '44021', '44021', '44021');
    }

    #[Test]
    public function codes_are_validated(): void
    {
        $this->expectException(InvalidArgumentException::class);

        LocationFilter::zipcodes('EH36AQ');
    }
}
