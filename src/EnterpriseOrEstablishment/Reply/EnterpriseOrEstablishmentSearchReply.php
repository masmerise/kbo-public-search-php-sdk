<?php declare(strict_types=1);

namespace KboPublicSearch\EnterpriseOrEstablishment\Reply;

use KboPublicSearch\Core\Reply\ReplyContext;
use KboPublicSearch\Core\Reply\SearchReply;
use KboPublicSearch\EnterpriseOrEstablishment\Type\EnterprisesOrEstablishments;

final readonly class EnterpriseOrEstablishmentSearchReply extends SearchReply
{
    /** @internal */
    public function __construct(ReplyContext $context, public EnterprisesOrEstablishments $enterprisesOrEstablishments)
    {
        parent::__construct($context);
    }
}
