<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Activity;

use KboPublicSearch\Core\Type\LocalizedText;
use KboPublicSearch\Core\Type\Period;

/** A NACEBEL activity. */
final readonly class Activity
{
    /** @internal */
    public function __construct(
        /** The NACEBEL code. */
        public string $code,
        public ?ActivityGroup $group,
        /** The raw Version, kept when it is not a known NACEBEL version. */
        public string $versionCode,
        public ?NaceVersion $version,
        public LocalizedText $description,
        public Period $period,
        public ?ActivityClassification $classification,
    ) {}
}
