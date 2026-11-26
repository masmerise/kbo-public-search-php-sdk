<?php declare(strict_types=1);

namespace KboPublicSearch\Entity\Type;

use KboPublicSearch\Core\Type\Address\Address;
use KboPublicSearch\Core\Type\Description;
use KboPublicSearch\Core\Type\Period;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Core\Type\Primitive\EstablishmentNumber;
use KboPublicSearch\Core\Type\Status\Status;

/** An establishment unit as the address and activity searches return it (cookbook §2.9.2). */
final readonly class Establishment
{
    /** @internal */
    public function __construct(
        public EstablishmentNumber $establishmentNumber,
        public Status $status,
        public Period $period,
        public ?EnterpriseNumber $enterpriseNumber,
        public ?Status $enterpriseStatus,
        /** PrimaryDenomination: the name in each requested language. */
        public Description $primaryDenomination,
        public ?Address $address,
    ) {}
}
