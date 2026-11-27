<?php declare(strict_types=1);

namespace KboPublicSearch\Authentication;

use KboPublicSearch\Core\Utility\XmlText;
use Stringable;
use Webmozart\Assert\Assert;

final readonly class Username implements Stringable
{
    private function __construct(private string $username)
    {
        Assert::notEmpty($username, 'The username must not be empty.');
        XmlText::assert($username, 'The username');
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
