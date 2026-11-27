<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit;

use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * An operation group's types are its own: another group may not use them. What two groups share has to live in Core.
 */
final class BoundariesTest extends TestCase
{
    /** The operation groups (bounded contexts). */
    private const array CONTEXTS = ['CodeList', 'Enterprise', 'EnterpriseOrEstablishment', 'Establishment', 'Street'];

    #[Test]
    #[DataProvider('contexts')]
    public function a_context_only_uses_core_and_itself(string $context): void
    {
        // Act
        $violations = self::importsOf("src/{$context}", array_diff(self::CONTEXTS, [$context]));

        // Assert
        $this->assertSame([], $violations, "{$context} uses another context's types; move what they share to Core.");
    }

    #[Test]
    public function core_uses_no_context(): void
    {
        // Act
        $violations = self::importsOf('src/Core', self::CONTEXTS);

        // Assert
        $this->assertSame([], $violations, 'Core may not depend on an operation group.');
    }

    /** @return iterable<string, array{string}> */
    public static function contexts(): iterable
    {
        foreach (self::CONTEXTS as $context) {
            yield $context => [$context];
        }
    }

    /**
     * @param list<string> $forbidden
     *
     * @return list<string> "file: imported class" for every import of a forbidden context
     */
    private static function importsOf(string $directory, array $forbidden): array
    {
        $pattern = '/^use (?:function )?KboPublicSearch\\\\(' . implode('|', $forbidden) . ')\\\\[^;]+;/m';
        $root = dirname(__DIR__, 2);
        $violations = [];

        /** @var SplFileInfo $file */
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator("{$root}/{$directory}")) as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            preg_match_all($pattern, (string) file_get_contents($file->getPathname()), $matches);

            foreach ($matches[0] as $import) {
                $violations[] = substr($file->getPathname(), strlen($root) + 1) . ': ' . $import;
            }
        }

        return $violations;
    }
}
