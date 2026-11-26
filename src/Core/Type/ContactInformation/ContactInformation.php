<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\ContactInformation;

use KboPublicSearch\Core\Type\Description;
use KboPublicSearch\Core\Type\Primitive\Date;

/** A phone number, fax number, website or e-mail address. */
final readonly class ContactInformation
{
    /** @internal */
    public function __construct(
        /** Optional in the XSD. */
        public ?ContactType $contactType,
        public Description $description,
        public string $value,
        public bool $foreignBranchOfficeFlag,
        public ?Date $begin,
    ) {}
}
