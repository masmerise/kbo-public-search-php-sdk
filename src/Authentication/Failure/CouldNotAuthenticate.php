<?php declare(strict_types=1);

namespace KboPublicSearch\Authentication\Failure;

use KboPublicSearch\Core\Failure\KboPublicSearchException;

class CouldNotAuthenticate extends KboPublicSearchException
{
    public static function because(string $faultString): self
    {
        return new self($faultString);
    }
}
