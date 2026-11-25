<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Primitive;

use DateTimeImmutable;
use Stringable;
use Webmozart\Assert\Assert;

/** A calendar date. Timezone offsets in replies ("1968-01-01+01:00", "2002-03-05Z") are dropped, never converted. */
final readonly class Date implements Stringable
{
    private function __construct(private string $date) {}

    public static function fromString(string $date): self
    {
        $matched = preg_match('/^(\d{4})-(\d{2})-(\d{2})(?:Z|[+-]\d{2}:\d{2})?$/', trim($date), $parts) === 1;

        Assert::true(
            $matched && checkdate((int) $parts[2], (int) $parts[3], (int) $parts[1]),
            "{$date} could not be parsed as a valid date. It must be in the format YYYY-MM-DD.",
        );

        return new self("{$parts[1]}-{$parts[2]}-{$parts[3]}");
    }

    public function toDateTimeImmutable(): DateTimeImmutable
    {
        /** @var DateTimeImmutable */
        return DateTimeImmutable::createFromFormat('!Y-m-d', $this->date);
    }

    public function toString(): string
    {
        return $this->date;
    }

    public function __toString(): string
    {
        return $this->date;
    }
}
