<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Authorization;

use KboPublicSearch\Core\Type\Description;
use KboPublicSearch\Core\Type\Period;

/** An authorization ("toelating"). */
final readonly class Authorization
{
    /** @internal */
    public function __construct(
        public string $code,
        public Description $description,
        public Period $period,
    ) {}
}
