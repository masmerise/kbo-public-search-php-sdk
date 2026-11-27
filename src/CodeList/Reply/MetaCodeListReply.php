<?php declare(strict_types=1);

namespace KboPublicSearch\CodeList\Reply;

use KboPublicSearch\CodeList\Type\MetaCodeList;
use KboPublicSearch\Core\Reply\Reply;
use KboPublicSearch\Core\Reply\ReplyContext;

final readonly class MetaCodeListReply extends Reply
{
    /** @internal */
    public function __construct(ReplyContext $context, public MetaCodeList $metaCodeList)
    {
        parent::__construct($context);
    }
}
