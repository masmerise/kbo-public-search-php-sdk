<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Address\Address;
use KboPublicSearch\Core\Type\Address\Addresses;
use KboPublicSearch\Core\Type\Address\Country;
use KboPublicSearch\Core\Type\Address\CountryCode;
use KboPublicSearch\Core\Type\Address\Municipality;
use KboPublicSearch\Core\Type\Address\Street;
use KboPublicSearch\Core\Type\Address\TechnicalCreationReason;
use KboPublicSearch\Core\Type\Address\TechnicalCreationReasonCode;
use KboPublicSearch\Core\Type\Address\TypeOfAddress;
use KboPublicSearch\Core\Type\Address\TypeOfAddressCode;
use KboPublicSearch\Core\Type\Primitive\MunicipalityCode;
use KboPublicSearch\Core\Type\Primitive\StreetCode;
use KboPublicSearch\Core\Xml\Element;

use function KboPublicSearch\Core\Utility\transform;

/** @internal */
final readonly class AddressMapper
{
    public function __construct(
        private DescriptionMapper $descriptionMapper = new DescriptionMapper(),
        private PeriodMapper $periodMapper = new PeriodMapper(),
    ) {}

    public function toAddress(Element $address): Address
    {
        $street = $address->child('Street');
        $municipality = $address->child('Municipality');

        return new Address(
            street: new Street(transform($street->optionalText('Code'), StreetCode::fromString(...)), $this->descriptionMapper->toDescription($street)),
            houseNumber: $address->optionalText('HouseNumber') ?? '',
            box: $address->optionalText('Box'),
            zipcode: $address->optionalText('Zipcode') ?? '',
            municipality: new Municipality(transform($municipality->optionalText('Code'), MunicipalityCode::fromString(...)), $this->descriptionMapper->toDescription($municipality)),
            extraAddressInfo: $address->optionalText('ExtraAddressInfo'),
            country: transform($address->optional('Country'), fn (Element $country) => new Country(
                CountryCode::from($country->textOf('Code')),
                $this->descriptionMapper->toDescription($country),
            )),
            begin: $this->periodMapper->toDate($address, 'Begin'),
            typeOfAddress: transform($address->optional('TypeOfAddress'), fn (Element $type) => new TypeOfAddress(
                TypeOfAddressCode::from($type->textOf('Code')),
                $this->descriptionMapper->toDescription($type),
            )),
            strikingOff: $this->periodMapper->toDate($address, 'StrikingOff'),
            technicalCreationReason: transform($address->optional('TechnicalCreationReason'), fn (Element $reason) => new TechnicalCreationReason(
                TechnicalCreationReasonCode::from($reason->textOf('Code')),
                $this->descriptionMapper->toDescription($reason),
            )),
        );
    }

    public function toAddresses(Element $parent): Addresses
    {
        return Addresses::of(array_map($this->toAddress(...), $parent->children('Address')));
    }
}
