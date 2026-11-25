<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Shared;

/** Whether an activity is a main, ancillary or secondary activity. */
final readonly class ActivityClassification
{
    /** @internal */
    public function __construct(
        public string $code,
        /** Null for a code this SDK does not know yet. */
        public ?ActivityClassificationKind $kind,
        public LocalizedText $description,
    ) {}
}
