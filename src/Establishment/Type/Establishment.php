<?php declare(strict_types=1);

namespace KboPublicSearch\Establishment\Type;

use KboPublicSearch\Core\Type\Activity\Activities;
use KboPublicSearch\Core\Type\Address\Address;
use KboPublicSearch\Core\Type\Authorization\Authorizations;
use KboPublicSearch\Core\Type\ContactInformation\ContactInformations;
use KboPublicSearch\Core\Type\Denomination\Denominations;
use KboPublicSearch\Core\Type\Description;
use KboPublicSearch\Core\Type\ExOfficioStrikingOff\ExOfficioStrikingOffs;
use KboPublicSearch\Core\Type\Period;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Core\Type\Primitive\EstablishmentNumber;
use KboPublicSearch\Core\Type\Status\Status;

/**
 * An establishment unit ("vestigingseenheid").
 *
 * TypeOfResult::Short fills $name; TypeOfResult::Long fills $denominations, $activities, $authorizations and
 * $enterpriseExOfficioStrikingOffs instead. What a level does not return is empty.
 */
final readonly class Establishment
{
    /** @internal */
    public function __construct(
        public EstablishmentNumber $number,
        public Status $status,
        public Period $period,
        /** Exceptionally absent on ReadEstablishmentByEstablishmentNumber. */
        public ?EnterpriseNumber $enterpriseNumber,
        public ?Status $enterpriseStatus,
        /** PrimaryDenomination (short): the name in each requested language. */
        public Description $name,
        /** Every name (long). */
        public Denominations $denominations,
        public ContactInformations $contactInformation,
        public ?Address $address,
        /** Long only. */
        public Activities $activities,
        /** Long only. */
        public Authorizations $authorizations,
        /** Ex officio striking offs of the enterprise (long only). */
        public ExOfficioStrikingOffs $enterpriseExOfficioStrikingOffs,
    ) {}
}
