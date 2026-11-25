<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

use KboPublicSearch\Core\Type\Primitive\Date;
use KboPublicSearch\Core\Type\Shared\LocalizedText;
use KboPublicSearch\Core\Type\Shared\Status;

/** The juridical situation ("rechtstoestand") and status of an enterprise. */
final readonly class JuridicalSituation
{
    /** @internal */
    public function __construct(
        public string $code,
        public LocalizedText $description,
        /** ValidityPeriod > Begin. */
        public ?Date $since,
        public Status $status,
        public ?JuridicalEvent $event,
    ) {}
}
