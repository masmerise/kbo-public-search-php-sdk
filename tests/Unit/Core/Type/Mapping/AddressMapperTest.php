<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Address\Address;
use KboPublicSearch\Core\Type\Address\Addresses;
use KboPublicSearch\Core\Type\Address\CountryCode;
use KboPublicSearch\Core\Type\Address\TechnicalCreationReasonCode;
use KboPublicSearch\Core\Type\Address\TypeOfAddressCode;
use KboPublicSearch\Core\Type\Mapping\AddressMapper;
use KboPublicSearch\Localization\Language;
use KboPublicSearch\Tests\Support\Snippet;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ValueError;

final class AddressMapperTest extends TestCase
{
    private Addresses $addresses;

    protected function setUp(): void
    {
        parent::setUp();

        $this->addresses = $this->addressMapper()->toAddresses(Snippet::load('shared/addresses'));
    }

    #[Test]
    public function registered_office(): void
    {
        // Act
        $address = $this->addresses[0];

        // Assert
        $this->assertInstanceOf(Address::class, $address);
        $this->assertSame('0086', $address->street->code?->toString());
        $this->assertSame('Vooruitgangsstraat', $address->street->description->in(Language::Dutch));
        $this->assertSame('50', $address->houseNumber);
        $this->assertNull($address->box);
        $this->assertSame('1210', $address->zipcode);
        $this->assertSame('21014', $address->municipality->code?->toString());
        $this->assertSame('Sint-Joost-ten-Node', (string) $address->municipality->description);
        $this->assertSame('City Atrium', $address->extraAddressInfo);
        $this->assertNull($address->country);
        $this->assertSame(TypeOfAddressCode::RegisteredOffice, $address->typeOfAddress?->code);
        $this->assertSame('2005-02-01', $address->begin?->toString());
        $this->assertNull($address->strikingOff);
        $this->assertNull($address->technicalCreationReason);
    }

    #[Test]
    public function struck_off(): void
    {
        $this->assertSame('2006-10-04', $this->addresses[1]->strikingOff?->toString());
    }

    #[Test]
    public function foreign(): void
    {
        // Act
        $address = $this->addresses[2];

        // Assert
        $this->assertNull($address->street->code);
        $this->assertNull($address->municipality->code);
        $this->assertSame('EH36AQ', $address->zipcode);
        $this->assertSame(CountryCode::UnitedKingdom, $address->country?->code);
        $this->assertSame('Groot-Brittannië', $address->country?->description->first());
    }

    #[Test]
    public function unknown_type_of_address_fails(): void
    {
        // Assert
        $this->expectException(ValueError::class);

        // Act
        $this->addressMapper()->toAddresses(Snippet::load('shared/unknown/type-of-address'));
    }

    #[Test]
    public function optional_parts(): void
    {
        // Act
        $address = $this->addresses[3];

        // Assert
        $this->assertSame('B', $address->box);
        $this->assertNull($address->typeOfAddress);
        $this->assertNull($address->begin);
        $this->assertSame(TechnicalCreationReasonCode::ModificationOfTheAddressAfterATechnicalChange, $address->technicalCreationReason?->code);
        $this->assertSame('Fusion de communes', $address->technicalCreationReason?->description->in(Language::French));
    }

    #[Test]
    public function absent_addresses_are_an_empty_collection(): void
    {
        $this->assertCount(0, $this->addressMapper()->toAddresses(Snippet::load('shared/statuses')));
        $this->assertCount(4, $this->addresses);
    }

    private function addressMapper(): AddressMapper
    {
        return new AddressMapper();
    }
}
