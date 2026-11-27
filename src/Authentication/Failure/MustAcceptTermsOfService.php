<?php declare(strict_types=1);

namespace KboPublicSearch\Authentication\Failure;

final class MustAcceptTermsOfService extends CouldNotAuthenticate
{
    /** The page where the terms of service must be accepted, if the fault mentions one. */
    public readonly ?string $url;

    private function __construct(string $faultString)
    {
        parent::__construct($faultString);

        $this->url = preg_match('/https?:\/\/[^\s!]+/', $faultString, $matches) === 1 ? $matches[0] : null;
    }

    public static function because(string $faultString): self
    {
        return new self($faultString);
    }
}
