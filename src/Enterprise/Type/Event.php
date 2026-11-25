<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

use KboPublicSearch\Core\Type\Description;
use KboPublicSearch\Core\Type\Primitive\Date;

/** An event on a juridical situation, e.g. a rehabilitation. */
final readonly class Event
{
    /** @internal */
    public function __construct(
        public string $code,
        public Description $description,
        public Date $date,
    ) {}
}
