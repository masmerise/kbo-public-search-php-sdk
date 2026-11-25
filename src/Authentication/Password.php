<?php declare(strict_types=1);

namespace KboPublicSearch\Authentication;

use SensitiveParameter;
use Webmozart\Assert\Assert;

final readonly class Password
{
    private function __construct(#[SensitiveParameter] private string $password)
    {
        Assert::notEmpty($password, 'The password must not be empty.');
    }

    public static function fromString(#[SensitiveParameter] string $password): self
    {
        return new self($password);
    }

    /** @internal Only used to compute the WS-Security password digest. */
    public function toString(): string
    {
        return $this->password;
    }

    /** @return array<string, string> */
    public function __debugInfo(): array
    {
        return ['password' => '********'];
    }
}
