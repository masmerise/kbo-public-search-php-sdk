<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Failure;

use Throwable;

final class UnknownException extends KboPublicSearchException
{
    public static function sorry(Throwable $previous): self
    {
        return new self($previous->getMessage(), (int) $previous->getCode(), $previous);
    }
}
