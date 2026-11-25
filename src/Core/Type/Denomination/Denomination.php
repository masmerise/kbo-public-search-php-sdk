<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Denomination;

use KboPublicSearch\Core\Type\Primitive\Date;

/** A name of an enterprise, establishment or branch, in one language. */
final readonly class Denomination
{
    /** @internal */
    public function __construct(
        public string $code,
        /** Null for a code this SDK does not know yet. */
        public ?DenominationKind $kind,
        public string $name,
        /** The language code as KBO returns it ("nl", "fr", "de"); empty for natural persons' names. */
        public string $language,
        public ?Date $since,
    ) {}
}
