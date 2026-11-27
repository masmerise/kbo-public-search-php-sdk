<?php declare(strict_types=1);

namespace KboPublicSearch\Street\Reply;

use KboPublicSearch\Core\Reply\Reply;
use KboPublicSearch\Core\Reply\ReplyContext;
use KboPublicSearch\Street\Type\StreetNames;

final readonly class StreetNameReply extends Reply
{
    /** @internal */
    public function __construct(ReplyContext $context, public StreetNames $streetNames)
    {
        parent::__construct($context);
    }
}
