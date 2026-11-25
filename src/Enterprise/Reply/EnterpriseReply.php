<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Reply;

use KboPublicSearch\Core\Reply\Reply;
use KboPublicSearch\Core\Reply\ReplyContext;
use KboPublicSearch\Enterprise\Type\Enterprise;

final readonly class EnterpriseReply extends Reply
{
    /** @internal */
    public function __construct(ReplyContext $context, public Enterprise $enterprise)
    {
        parent::__construct($context);
    }
}
