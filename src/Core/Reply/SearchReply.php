<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Reply;

/** A search's reply: at most 200 results (cookbook §2.2.2). */
abstract readonly class SearchReply extends Reply
{
    /** Whether KBO found more than 200 matches and returned only the first 200 (status 00008). */
    public function isTruncated(): bool
    {
        return $this->context->status->isTruncated();
    }
}
