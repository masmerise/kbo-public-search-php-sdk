<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Shared;

use KboPublicSearch\Core\Type\Primitive\Date;

/** A validity period; End is only present once it has ended. */
final readonly class Period
{
    /** @internal */
    public function __construct(
        public ?Date $begin,
        public ?Date $end,
    ) {}
}
