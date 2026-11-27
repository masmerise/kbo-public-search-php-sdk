<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit;

use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/** Lints every PHP example in the README; it does not run them. */
final class ReadmeExamplesTest extends TestCase
{
    #[Test]
    public function every_php_example_is_valid_syntax(): void
    {
        // Arrange
        $readme = file_get_contents(__DIR__ . '/../../README.md');
        preg_match_all('/```php\n(.*?)```/s', (string) $readme, $blocks);

        $this->assertNotEmpty($blocks[1], 'The README has no PHP examples.');
        $leftovers = glob(sys_get_temp_dir() . '/readme*') ?: [];

        foreach ($blocks[1] as $index => $code) {
            $file = (string) tempnam(sys_get_temp_dir(), 'readme'); // php -l does not need a .php extension
            file_put_contents($file, "<?php\n{$code}");

            // Act
            exec(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($file) . ' 2>&1', $output, $exitCode);
            unlink($file);

            // Assert
            $this->assertSame(0, $exitCode, "README example #{$index} is not valid PHP:\n{$code}\n" . implode("\n", $output));
            $output = [];
        }

        $this->assertSame($leftovers, glob(sys_get_temp_dir() . '/readme*') ?: [], 'Temporary files were left behind.');
    }
}
