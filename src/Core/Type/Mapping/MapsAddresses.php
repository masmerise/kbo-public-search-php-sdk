<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Shared\Address;
use KboPublicSearch\Core\Type\Shared\Addresses;
use KboPublicSearch\Core\Type\Shared\AddressKind;
use KboPublicSearch\Core\Type\Shared\CodedValue;
use KboPublicSearch\Core\Type\Shared\Country;
use KboPublicSearch\Core\Type\Shared\Municipality;
use KboPublicSearch\Core\Type\Shared\Street;
use KboPublicSearch\Core\Type\Shared\TypeOfAddress;
use KboPublicSearch\Core\Xml\Element;

use function KboPublicSearch\Core\Utility\transform;

/** @internal */
trait MapsAddresses
{
    use MapsLocalizedTexts;
    use MapsPeriods;

    private function toAddress(Element $address): Address
    {
        $street = $address->child('Street');
        $municipality = $address->child('Municipality');

        return new Address(
            street: new Street($street->optionalText('Code'), $this->toLocalizedText($street)),
            houseNumber: $address->optionalText('HouseNumber') ?? '',
            box: $address->optionalText('Box'),
            zipcode: $address->optionalText('Zipcode') ?? '',
            municipality: new Municipality($municipality->optionalText('Code'), $this->toLocalizedText($municipality)),
            extraAddressInfo: $address->optionalText('ExtraAddressInfo'),
            country: transform($address->optional('Country'), fn (Element $country) => new Country(
                $country->textOf('Code'),
                $this->toLocalizedText($country),
            )),
            since: $this->toDate($address, 'Begin'),
            type: transform($address->optional('TypeOfAddress'), fn (Element $type) => new TypeOfAddress(
                AddressKind::from($type->textOf('Code')),
                $this->toLocalizedText($type),
            )),
            strikingOff: $this->toDate($address, 'StrikingOff'),
            technicalCreationReason: transform($address->optional('TechnicalCreationReason'), fn (Element $reason) => new CodedValue(
                $reason->textOf('Code'),
                $this->toLocalizedText($reason),
            )),
        );
    }

    private function toAddresses(Element $parent): Addresses
    {
        return Addresses::of(array_map($this->toAddress(...), $parent->children('Address')));
    }
}
