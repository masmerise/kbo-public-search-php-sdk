<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Address;

use KboPublicSearch\Core\Type\Description;
use KboPublicSearch\Core\Type\Primitive\StreetCode;

/** A street. */
final readonly class Street
{
    /** @internal */
    public function __construct(
        /** Null when KBO has no street code, e.g. for foreign addresses. */
        public ?StreetCode $code,
        public Description $description,
    ) {}
}
