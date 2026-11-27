<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Reply;

/** Every operation answers with a Reply: its payload plus the ReplyContext. */
abstract readonly class Reply
{
    public function __construct(public ReplyContext $context) {}
}
