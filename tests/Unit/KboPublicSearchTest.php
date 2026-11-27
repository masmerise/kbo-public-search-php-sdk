<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit;

use KboPublicSearch\Authentication\Credentials;
use KboPublicSearch\KboPublicSearch;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class KboPublicSearchTest extends TestCase
{
    #[Test]
    public function exposes_cookbook_version(): void
    {
        $this->assertSame('R018.08', KboPublicSearch::VERSION);
    }

    #[Test]
    public function fluent_switches_return_same_instance(): void
    {
        // Arrange
        $kbo = KboPublicSearch::authenticate(Credentials::present('userid', 'secret'));

        // Assert
        $this->assertSame($kbo, $kbo->useTest()->useFrench());
        $this->assertSame($kbo, $kbo->useProduction()->useDutch()->useDutchAndFrench());
    }
}
