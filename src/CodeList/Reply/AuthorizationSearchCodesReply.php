<?php declare(strict_types=1);

namespace KboPublicSearch\CodeList\Reply;

use KboPublicSearch\CodeList\Type\AuthorizationSearchCodes;
use KboPublicSearch\Core\Reply\Reply;
use KboPublicSearch\Core\Reply\ReplyContext;

final readonly class AuthorizationSearchCodesReply extends Reply
{
    /** @internal */
    public function __construct(ReplyContext $context, public AuthorizationSearchCodes $authorizationSearchCodes)
    {
        parent::__construct($context);
    }
}
