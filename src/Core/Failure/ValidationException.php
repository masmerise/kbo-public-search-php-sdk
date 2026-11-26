<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Failure;

use KboPublicSearch\Core\Reply\ReplyContext;
use KboPublicSearch\Core\Reply\ReplyStatus;

/** KBO answered, but with a ReplyStatus this operation does not accept (e.g. 00010 Enterprisenumber does not exist). */
abstract class ValidationException extends KboPublicSearchException
{
    public readonly ReplyStatus $status;

    final public function __construct(public readonly ReplyContext $context)
    {
        parent::__construct($context->statusDescription, (int) $context->status->value);

        $this->status = $context->status;
    }
}
