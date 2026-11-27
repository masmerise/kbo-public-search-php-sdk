<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Denomination;

use KboPublicSearch\Core\Type\Period;
use KboPublicSearch\Localization\Language;

/** A name of an enterprise, establishment or branch, in one language. */
final readonly class Denomination
{
    /** @internal */
    public function __construct(
        public DenominationCode $code,
        public string $name,
        /** Null for physical persons' names, which KBO sends without a language. */
        public ?Language $language,
        public ?Period $validityPeriod,
    ) {}
}
