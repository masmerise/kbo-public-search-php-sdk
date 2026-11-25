<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Failure;

use Throwable;

final class UnknownException extends KboPublicSearchException
{
    public static function sorry(Throwable $previous): self
    {
        return new self($previous->getMessage(), (int) $previous->getCode(), $previous);
    }

    /** A response that is not a KBO reply, e.g. a proxy's error page; the message names the HTTP status and the body's start. */
    public static function unexpectedReply(int $status, string $body, Throwable $previous): self
    {
        $excerpt = trim(preg_replace('/\s+/', ' ', $body) ?? '');
        $excerpt = $excerpt === '' ? 'empty body' : (mb_strlen($excerpt) > 200 ? mb_substr($excerpt, 0, 200) . '…' : $excerpt);

        return new self("{$previous->getMessage()} (HTTP {$status}: {$excerpt})", (int) $previous->getCode(), $previous);
    }
}
