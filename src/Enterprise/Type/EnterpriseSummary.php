<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

use KboPublicSearch\Core\Type\Address\Address;
use KboPublicSearch\Core\Type\LocalizedText;
use KboPublicSearch\Core\Type\Period;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Core\Type\Status\Status;

/** An enterprise as searches return it: fewer elements than ReadEnterprise (cookbook §2.2.2). */
final readonly class EnterpriseSummary
{
    /** @internal */
    public function __construct(
        public EnterpriseNumber $number,
        /** Status, or JuridicalSituation > Status where KBO sends that instead. */
        public ?Status $status,
        public Period $period,
        public TypeOfEnterprise $type,
        /** Only for legal persons. */
        public ?JuridicalForm $juridicalForm,
        /** PrimaryDenomination: the name in each requested language. */
        public LocalizedText $name,
        /** The registered office; absent for natural persons. */
        public ?Address $address,
        public int $businessUnits,
    ) {}
}
