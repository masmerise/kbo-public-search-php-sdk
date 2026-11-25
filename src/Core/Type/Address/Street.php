<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Address;

use KboPublicSearch\Core\Type\Description;

/** A street. */
final readonly class Street
{
    /** @internal */
    public function __construct(
        /** Null when KBO has no street code, e.g. for foreign addresses. */
        public ?string $code,
        public Description $description,
    ) {}
}
