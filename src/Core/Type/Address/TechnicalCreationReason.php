<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Address;

use KboPublicSearch\Core\Type\LocalizedText;

/** Why an address was created for technical reasons, e.g. a merger of municipalities that changed NIS codes. */
final readonly class TechnicalCreationReason
{
    /** @internal */
    public function __construct(
        public string $code,
        public LocalizedText $description,
    ) {}
}
