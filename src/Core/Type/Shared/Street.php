<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Shared;

/** A street. */
final readonly class Street
{
    /** @internal */
    public function __construct(
        /** Null when KBO has no street code, e.g. for foreign addresses. */
        public ?string $code,
        public LocalizedText $name,
    ) {}
}
