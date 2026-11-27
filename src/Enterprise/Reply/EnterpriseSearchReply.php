<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Reply;

use KboPublicSearch\Core\Reply\ReplyContext;
use KboPublicSearch\Core\Reply\SearchReply;
use KboPublicSearch\Enterprise\Type\EnterpriseSummaries;

final readonly class EnterpriseSearchReply extends SearchReply
{
    /** @internal */
    public function __construct(ReplyContext $context, public EnterpriseSummaries $enterprises)
    {
        parent::__construct($context);
    }
}
