<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Contact;

use KboPublicSearch\Core\Type\LocalizedText;
use KboPublicSearch\Core\Type\Primitive\Date;

/** A phone number, fax number, website or e-mail address. */
final readonly class ContactInformation
{
    /** @internal */
    public function __construct(
        /** The raw ContactType, if any. */
        public ?string $type,
        /** Null when the type is absent or unknown. */
        public ?ContactKind $kind,
        public LocalizedText $description,
        public string $value,
        public bool $foreignBranchOffice,
        public ?Date $since,
    ) {}
}
