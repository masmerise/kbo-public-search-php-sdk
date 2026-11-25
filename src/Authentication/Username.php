<?php declare(strict_types=1);

namespace KboPublicSearch\Authentication;

use Stringable;
use Webmozart\Assert\Assert;

final readonly class Username implements Stringable
{
    private function __construct(private string $username)
    {
        Assert::notEmpty($username, 'The username must not be empty.');
    }

    public static function fromString(string $username): self
    {
        return new self($username);
    }

    public function toString(): string
    {
        return $this->username;
    }

    public function __toString(): string
    {
        return $this->username;
    }
}
