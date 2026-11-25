<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

use KboPublicSearch\Core\Type\Description;
use KboPublicSearch\Core\Type\Period;
use KboPublicSearch\Core\Type\Status\Status;

/** The juridical situation ("rechtstoestand") and status of an enterprise. */
final readonly class JuridicalSituation
{
    /** @internal */
    public function __construct(
        public string $code,
        public Description $description,
        public ?Period $validityPeriod,
        public Status $status,
        public ?Event $event,
    ) {}
}
