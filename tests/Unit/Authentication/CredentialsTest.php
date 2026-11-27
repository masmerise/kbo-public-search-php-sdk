<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Authentication;

use InvalidArgumentException;
use KboPublicSearch\Authentication\Credentials;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class CredentialsTest extends TestCase
{
    #[Test]
    public function username_must_not_be_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Credentials::present('', 'secret');
    }

    #[Test]
    public function password_must_not_be_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Credentials::present('userid', '');
    }

    #[Test]
    public function password_is_not_dumped(): void
    {
        // Act
        $dump = print_r(Credentials::present('userid', 'secret'), true);

        // Assert
        $this->assertStringNotContainsString('secret', $dump);
        $this->assertStringContainsString('userid', $dump);
    }
}
