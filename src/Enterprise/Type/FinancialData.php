<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

use KboPublicSearch\Core\Type\Primitive\Date;
use KboPublicSearch\Core\Type\Shared\Period;

/** Annual meeting and fiscal year data. */
final readonly class FinancialData
{
    /** @internal */
    public function __construct(
        public ?int $annualMeetingMonth,
        public ?int $fiscalYearEndDay,
        public ?int $fiscalYearEndMonth,
        public ?Period $exceptionalFiscalYear,
        /** ValidityPeriod > Begin. */
        public ?Date $since,
    ) {}
}
