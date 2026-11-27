<?php declare(strict_types=1);

namespace KboPublicSearch\Establishment\Reply;

use KboPublicSearch\Core\Reply\ReplyContext;
use KboPublicSearch\Core\Reply\SearchReply;
use KboPublicSearch\Establishment\Type\Establishments;

final readonly class EstablishmentSearchReply extends SearchReply
{
    /** @internal */
    public function __construct(ReplyContext $context, public Establishments $establishments)
    {
        parent::__construct($context);
    }
}
