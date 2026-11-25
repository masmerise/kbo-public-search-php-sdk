<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Http;

use KboPublicSearch\Core\Reply\ReplyContext;
use KboPublicSearch\Core\Xml\Element;

/** @internal */
final readonly class Result
{
    public function __construct(
        public ReplyContext $context,
        /** The first element of soap:Body, e.g. ReadEnterpriseReply. */
        public Element $body,
    ) {}
}
