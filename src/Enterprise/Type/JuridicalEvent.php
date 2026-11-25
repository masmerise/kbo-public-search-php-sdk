<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

use KboPublicSearch\Core\Type\Primitive\Date;
use KboPublicSearch\Core\Type\Shared\LocalizedText;

/** An event on a juridical situation, e.g. a rehabilitation. */
final readonly class JuridicalEvent
{
    /** @internal */
    public function __construct(
        public string $code,
        public LocalizedText $description,
        public Date $date,
    ) {}
}
