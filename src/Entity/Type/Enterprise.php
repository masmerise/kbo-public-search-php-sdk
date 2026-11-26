<?php declare(strict_types=1);

namespace KboPublicSearch\Entity\Type;

use KboPublicSearch\Core\Type\Address\Address;
use KboPublicSearch\Core\Type\Description;
use KboPublicSearch\Core\Type\Period;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Core\Type\Status\Status;
use KboPublicSearch\Core\Type\TypeOfEnterprise;

/** An enterprise as the address and activity searches return it (cookbook §2.9.2). */
final readonly class Enterprise
{
    /** @internal */
    public function __construct(
        public EnterpriseNumber $number,
        /** Status, or JuridicalSituation > Status where KBO sends that instead. */
        public ?Status $status,
        public Period $period,
        public TypeOfEnterprise $typeOfEnterprise,
        /** PrimaryDenomination: the name in each requested language. */
        public Description $primaryDenomination,
        /** The registered office; absent for physical persons. */
        public ?Address $address,
        public int $businessUnits,
    ) {}
}
