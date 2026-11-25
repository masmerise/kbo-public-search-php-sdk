<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Type;

use Closure;
use InvalidArgumentException;
use KboPublicSearch\Authentication\Username;
use KboPublicSearch\Core\Type\Parameter\Phonemes;
use KboPublicSearch\Core\Type\Primitive\HouseNumber;
use KboPublicSearch\Core\Type\Primitive\StreetName;
use KboPublicSearch\Enterprise\Type\Primitive\AuthorizationCode;
use KboPublicSearch\Enterprise\Type\Primitive\CompanyName;
use KboPublicSearch\Enterprise\Type\Primitive\FirstName;
use KboPublicSearch\Enterprise\Type\Primitive\LastName;
use KboPublicSearch\Establishment\Type\EstablishmentName;
use KboPublicSearch\Street\Type\PartOfStreetName;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/** Free text ends up in the envelope; text that is not valid XML 1.0 must be rejected before a request is sent. */
final class XmlSafeTextTest extends TestCase
{
    /** @param Closure(string): mixed $build */
    #[Test]
    #[DataProvider('inputs')]
    public function rejects_invalid_utf8_and_control_characters(Closure $build, string $value): void
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Act
        $build($value);
    }

    public static function inputs(): iterable
    {
        $types = [
            'username' => Username::fromString(...),
            'last name' => LastName::fromString(...),
            'first name' => FirstName::fromString(...),
            'company name' => CompanyName::fromString(...),
            'establishment name' => EstablishmentName::fromString(...),
            'street name' => StreetName::fromString(...),
            'house number' => HouseNumber::fromString(...),
            'part of street name' => PartOfStreetName::fromString(...),
            'authorization code' => AuthorizationCode::fromString(...),
            'phoneme' => static fn (string $value) => Phonemes::of($value),
        ];

        foreach ($types as $name => $build) {
            yield "{$name}, invalid utf-8" => [$build, "ab\xffc"];
            yield "{$name}, control character" => [$build, "ab\x01c"];
        }
    }

    #[Test]
    public function accepts_accented_text(): void
    {
        $this->assertSame('Société Générale', CompanyName::fromString('Société Générale')->toString());
        $this->assertSame(['Économie'], Phonemes::of('Économie')->toArray());
    }
}
