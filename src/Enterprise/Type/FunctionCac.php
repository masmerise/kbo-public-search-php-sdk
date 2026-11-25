<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

use KboPublicSearch\Core\Type\LocalizedText;
use KboPublicSearch\Core\Type\Period;

/** The function as it must be read under the Code of Companies and Associations (WVV/CSA). */
final readonly class FunctionCac
{
    /** @internal */
    public function __construct(
        public string $code,
        public LocalizedText $description,
        /** CACPeriod. */
        public Period $period,
    ) {}
}
