<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Address\Address;
use KboPublicSearch\Core\Type\Address\Addresses;
use KboPublicSearch\Core\Type\Address\AddressKind;
use KboPublicSearch\Core\Type\Address\Country;
use KboPublicSearch\Core\Type\Address\Municipality;
use KboPublicSearch\Core\Type\Address\Street;
use KboPublicSearch\Core\Type\Address\TechnicalCreationReason;
use KboPublicSearch\Core\Type\Address\TypeOfAddress;
use KboPublicSearch\Core\Xml\Element;

use function KboPublicSearch\Core\Utility\transform;

/** @internal */
trait MapsAddresses
{
    use MapsDescriptions;
    use MapsPeriods;

    private function toAddress(Element $address): Address
    {
        $street = $address->child('Street');
        $municipality = $address->child('Municipality');

        return new Address(
            street: new Street($street->optionalText('Code'), $this->toDescription($street)),
            houseNumber: $address->optionalText('HouseNumber') ?? '',
            box: $address->optionalText('Box'),
            zipcode: $address->optionalText('Zipcode') ?? '',
            municipality: new Municipality($municipality->optionalText('Code'), $this->toDescription($municipality)),
            extraAddressInfo: $address->optionalText('ExtraAddressInfo'),
            country: transform($address->optional('Country'), fn (Element $country) => new Country(
                $country->textOf('Code'),
                $this->toDescription($country),
            )),
            since: $this->toDate($address, 'Begin'),
            type: transform($address->optional('TypeOfAddress'), fn (Element $type) => new TypeOfAddress(
                $code = $type->textOf('Code'),
                AddressKind::tryFrom($code),
                $this->toDescription($type),
            )),
            strikingOff: $this->toDate($address, 'StrikingOff'),
            technicalCreationReason: transform($address->optional('TechnicalCreationReason'), fn (Element $reason) => new TechnicalCreationReason(
                $reason->textOf('Code'),
                $this->toDescription($reason),
            )),
        );
    }

    private function toAddresses(Element $parent): Addresses
    {
        return Addresses::of(array_map($this->toAddress(...), $parent->children('Address')));
    }
}
