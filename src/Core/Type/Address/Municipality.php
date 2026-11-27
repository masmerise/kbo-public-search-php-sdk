<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Address;

use KboPublicSearch\Core\Type\Description;
use KboPublicSearch\Core\Type\Primitive\MunicipalityCode;

/** A municipality. */
final readonly class Municipality
{
    /** @internal */
    public function __construct(
        /** The NIS code; null for foreign addresses. */
        public ?MunicipalityCode $code,
        public Description $description,
    ) {}
}
