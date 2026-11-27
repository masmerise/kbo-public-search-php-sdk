<?php declare(strict_types=1);

namespace KboPublicSearch\Authentication\Failure;

/** The username or password is wrong, or the clock is off by more than the timestamp allows. */
final class InvalidCredentials extends CouldNotAuthenticate
{
    public static function because(string $faultString): self
    {
        return new self($faultString);
    }
}
