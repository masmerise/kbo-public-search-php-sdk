<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

use KboPublicSearch\Core\Type\Description;
use KboPublicSearch\Core\Type\Period;

/** The juridical form of a legal person. */
final readonly class JuridicalForm
{
    /** @internal */
    public function __construct(
        public JuridicalFormCode $code,
        public Description $description,
        public ?Period $validityPeriod,
        public ?JuridicalFormCac $juridicalFormCac,
    ) {}
}
