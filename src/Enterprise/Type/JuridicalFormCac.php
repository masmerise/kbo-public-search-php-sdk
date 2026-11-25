<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

use KboPublicSearch\Core\Type\Primitive\Date;
use KboPublicSearch\Core\Type\Shared\LocalizedText;

/** The juridical form as it must be read under the Code of Companies and Associations (WVV/CSA) until the articles are amended. */
final readonly class JuridicalFormCac
{
    /** @internal */
    public function __construct(
        public string $code,
        public LocalizedText $description,
        public Date $date,
    ) {}
}
