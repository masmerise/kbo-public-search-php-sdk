<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Reply;

use KboPublicSearch\Core\Reply\Reply;
use KboPublicSearch\Core\Reply\ReplyContext;
use KboPublicSearch\Core\Reply\ReportsTruncation;
use KboPublicSearch\Enterprise\Type\EnterpriseSummaries;

final readonly class EnterpriseSearchReply extends Reply
{
    use ReportsTruncation;

    /** @internal */
    public function __construct(ReplyContext $context, public EnterpriseSummaries $enterprises)
    {
        parent::__construct($context);
    }
}
