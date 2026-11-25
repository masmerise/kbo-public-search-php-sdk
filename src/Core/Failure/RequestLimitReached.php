<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Failure;

final class RequestLimitReached extends KboPublicSearchException
{
    public static function because(string $faultString): self
    {
        return new self($faultString);
    }
}
