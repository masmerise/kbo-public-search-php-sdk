<?php declare(strict_types=1);

namespace KboPublicSearch\Establishment\Reply;

use KboPublicSearch\Core\Reply\Reply;
use KboPublicSearch\Core\Reply\ReplyContext;
use KboPublicSearch\Establishment\Type\Establishment;

final readonly class EstablishmentReply extends Reply
{
    /** @internal */
    public function __construct(ReplyContext $context, public Establishment $establishment)
    {
        parent::__construct($context);
    }
}
