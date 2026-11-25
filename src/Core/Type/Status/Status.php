<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Status;

use KboPublicSearch\Core\Type\LocalizedText;

/** The status of an enterprise, establishment or branch. */
final readonly class Status
{
    /** @internal */
    public function __construct(
        public string $code,
        /** Null for a code this SDK does not know yet. */
        public ?StatusKind $kind,
        public LocalizedText $description,
    ) {}
}
