<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Reply;

/** @mixin Reply */
trait ReportsTruncation
{
    /** Whether KBO found more than 200 matches and returned only the first 200 (status 00008). */
    public function isTruncated(): bool
    {
        return $this->context->status->isTruncated();
    }
}
