<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Type;

use KboPublicSearch\Core\Type\Collection;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;

final class CollectionTest extends TestCase
{
    #[Test]
    public function it_counts_and_returns_the_first_item(): void
    {
        // Act
        $numbers = Numbers::of([1, 2]);

        // Assert
        $this->assertSame(2, $numbers->count());
        $this->assertSame(1, $numbers->first());
    }

    #[Test]
    public function an_empty_collection_has_no_first_item(): void
    {
        $this->assertNull(Numbers::empty()->first());
    }

    #[Test]
    public function it_is_immutable_through_array_access(): void
    {
        // Arrange
        $numbers = Numbers::of([1, 2]);

        // Assert
        $this->expectException(RuntimeException::class);

        // Act
        $numbers[0] = 3;
    }
}

/** @extends Collection<int> */
final readonly class Numbers extends Collection {}
