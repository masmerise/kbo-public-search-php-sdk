<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Primitive;

use Stringable;
use Webmozart\Assert\Assert;

/** An establishment unit number ("vestigingseenheidsnummer"), normalised to 10 digits. */
final readonly class EstablishmentNumber implements Stringable
{
    private function __construct(private string $number)
    {
        Assert::regex($number, '/^[2-9]\d{9}$/', 'The establishment number must consist of 10 digits and start with 2 to 9.');
        Assert::true(EnterpriseNumber::passesChecksum($number), "The establishment number {$number} does not pass the modulo 97 check.");
    }

    /** Accepts "2.147.241.191" and "2147241191". */
    public static function fromString(string $number): self
    {
        return new self(preg_replace('/\D/', '', $number) ?? '');
    }

    public function equals(EstablishmentNumber $other): bool
    {
        return $this->number === $other->number;
    }

    public function toString(): string
    {
        return $this->number;
    }

    public function __toString(): string
    {
        return $this->number;
    }
}
