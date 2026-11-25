<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Http\Mapping;

use KboPublicSearch\Core\Http\UnwrappedReply;
use KboPublicSearch\Core\Reply\ReplyContext;
use KboPublicSearch\Core\Reply\ReplyStatus;
use KboPublicSearch\Core\Type\Primitive\Date;
use KboPublicSearch\Core\Xml\Element;
use RuntimeException;

use function KboPublicSearch\Core\Utility\transform;

/** @internal */
final readonly class UnwrappedReplyMapper
{
    /** @throws RuntimeException when the envelope lacks a reply context or a reply body */
    public function __invoke(Element $envelope): UnwrappedReply
    {
        $header = $envelope->child('Header')->child('ReplyContext');
        $status = $header->child('ReplyStatus');
        $code = $status->textOf('Code');
        $body = $envelope->child('Body')->elements()[0] ?? throw new RuntimeException('The reply body is empty.');

        return new UnwrappedReply(
            context: new ReplyContext(
                requestId: $header->textOf('RequestId'),
                replyId: $header->textOf('ReplyId'),
                status: ReplyStatus::fromCode($code),
                statusCode: $code,
                statusDescription: $status->optionalText('Description') ?? '',
                accountBalance: transform($header->optionalText('AccountBalance'), intval(...)),
                snapshotDate: transform($header->optionalText('SnapshotDate'), Date::fromString(...)),
            ),
            body: $body,
        );
    }
}
