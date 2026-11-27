<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Primitive;

use Stringable;
use Webmozart\Assert\Assert;

/** An enterprise number ("ondernemingsnummer"), normalised to 10 digits. */
final readonly class EnterpriseNumber implements Stringable
{
    private function __construct(private string $number)
    {
        Assert::regex($number, '/^[01]\d{9}$/', 'The enterprise number must consist of 10 digits and start with 0 or 1.');
        Assert::true(self::passesChecksum($number), "The enterprise number {$number} does not pass the modulo 97 check.");
    }

    /** Accepts "0314.595.348", "BE0314595348", "0314 595 348" and the 9-digit form "314595348". */
    public static function fromString(string $number): self
    {
        $digits = preg_replace('/\D/', '', preg_replace('/^\s*be/i', '', $number) ?? '') ?? '';

        if (strlen($digits) === 9) {
            $digits = "0{$digits}";
        }

        return new self($digits);
    }

    /** @internal */
    public static function passesChecksum(string $number): bool
    {
        return 97 - ((int) substr($number, 0, 8) % 97) === (int) substr($number, 8, 2);
    }

    public function equals(EnterpriseNumber $other): bool
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
