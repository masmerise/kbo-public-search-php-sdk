<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Status;

use KboPublicSearch\Core\Type\Description;

/** The status of an enterprise, establishment or branch. */
final readonly class Status
{
    /** @internal */
    public function __construct(
        public StatusCode $code,
        public Description $description,
    ) {}
}
