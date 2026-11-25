<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

use KboPublicSearch\Core\Type\LocalizedText;
use KboPublicSearch\Core\Type\Primitive\Date;

/** The juridical form of a legal person. */
final readonly class JuridicalForm
{
    /** @internal */
    public function __construct(
        public string $code,
        public LocalizedText $description,
        /** ValidityPeriod > Begin. */
        public ?Date $since,
        public ?JuridicalFormCac $cac,
    ) {}
}
