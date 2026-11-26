<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

use KboPublicSearch\Core\Type\Description;
use KboPublicSearch\Core\Type\Period;
use KboPublicSearch\Enterprise\Type\Primitive\QualificationCode;

/** A qualification ("hoedanigheid"). */
final readonly class Qualification
{
    /** @internal */
    public function __construct(
        public QualificationCode $code,
        public Description $description,
        public Period $period,
    ) {}
}
