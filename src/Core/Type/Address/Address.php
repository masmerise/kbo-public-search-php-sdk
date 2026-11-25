<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Address;

use KboPublicSearch\Core\Type\Primitive\Date;

/** An address. */
final readonly class Address
{
    /** @internal */
    public function __construct(
        public Street $street,
        public string $houseNumber,
        public ?string $box,
        /** A plain string: foreign zipcodes such as "EH36AQ" occur. */
        public string $zipcode,
        public Municipality $municipality,
        public ?string $extraAddressInfo,
        public ?Country $country,
        /** The date the address applies from (Begin). */
        public ?Date $since,
        public ?TypeOfAddress $type,
        public ?Date $strikingOff,
        public ?TechnicalCreationReason $technicalCreationReason,
    ) {}
}
