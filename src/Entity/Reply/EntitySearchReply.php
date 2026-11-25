<?php declare(strict_types=1);

namespace KboPublicSearch\Entity\Reply;

use KboPublicSearch\Core\Reply\Reply;
use KboPublicSearch\Core\Reply\ReplyContext;
use KboPublicSearch\Core\Reply\ReportsTruncation;
use KboPublicSearch\Entity\Type\Entities;

final readonly class EntitySearchReply extends Reply
{
    use ReportsTruncation;

    /** @internal */
    public function __construct(ReplyContext $context, public Entities $entities)
    {
        parent::__construct($context);
    }
}
