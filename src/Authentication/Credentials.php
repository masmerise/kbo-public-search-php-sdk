<?php declare(strict_types=1);

namespace KboPublicSearch\Authentication;

use SensitiveParameter;

final readonly class Credentials
{
    private function __construct(
        public Username $username,
        public Password $password,
    ) {}

    public static function present(string $username, #[SensitiveParameter] string $password): self
    {
        return new self(
            Username::fromString($username),
            Password::fromString($password),
        );
    }
}
