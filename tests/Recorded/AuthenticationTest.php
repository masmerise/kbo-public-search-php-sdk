<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Recorded;

use KboPublicSearch\Authentication\Failure\InvalidCredentials;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Enterprise\Find\Request\ReadEnterpriseRequest;
use KboPublicSearch\Environments\Environment;
use KboPublicSearch\KboPublicSearch;
use KboPublicSearch\Tests\RecordedTestCase;
use PHPUnit\Framework\Attributes\Test;

final class AuthenticationTest extends RecordedTestCase
{
    #[Test]
    public function wrong_password(): void
    {
        // Arrange
        $this->record(ReadEnterpriseRequest::class, 'authentication/wrong-password');
        $kbo = KboPublicSearch::authenticate(self::credentials('wrong'));
        self::environment() === Environment::Production ? $kbo->useProduction() : $kbo->useTest();

        // Assert
        $this->expectException(InvalidCredentials::class);

        // Act
        $kbo->enterprises->find(EnterpriseNumber::fromString(self::ENTERPRISE));
    }
}
