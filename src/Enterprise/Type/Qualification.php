<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

use KboPublicSearch\Core\Type\Shared\LocalizedText;
use KboPublicSearch\Core\Type\Shared\Period;

/** A qualification ("hoedanigheid"). */
final readonly class Qualification
{
    /** @internal */
    public function __construct(
        public string $code,
        public LocalizedText $description,
        public Period $period,
    ) {}
}
