<?php declare(strict_types=1);

namespace KboPublicSearch\CodeList\Reply;

use KboPublicSearch\CodeList\Type\CodeList;
use KboPublicSearch\Core\Reply\Reply;
use KboPublicSearch\Core\Reply\ReplyContext;

final readonly class CodeListReply extends Reply
{
    /** @internal */
    public function __construct(ReplyContext $context, public CodeList $codeList)
    {
        parent::__construct($context);
    }
}
