<?php declare(strict_types=1);

namespace KboPublicSearch\Establishment\Reply;

use KboPublicSearch\Core\Reply\Reply;
use KboPublicSearch\Core\Reply\ReplyContext;
use KboPublicSearch\Establishment\Type\Establishments;

/** Every active establishment unit of an enterprise. Unlike a search, the list is never truncated (cookbook §2.5.2). */
final readonly class EstablishmentsOfEnterpriseReply extends Reply
{
    /** @internal */
    public function __construct(ReplyContext $context, public Establishments $establishments)
    {
        parent::__construct($context);
    }
}
