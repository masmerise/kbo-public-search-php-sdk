<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Mapping\AuthorizationMapper;
use KboPublicSearch\Tests\Support\Snippet;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class AuthorizationMapperTest extends TestCase
{
    #[Test]
    public function maps_code_description_and_period(): void
    {
        // Act
        $authorization = $this->authorizationMapper()->toAuthorizations(Snippet::load('shared/authorizations'))->first();

        // Assert
        $this->assertSame('00504', $authorization?->code->toString());
        $this->assertSame('Erkend door de Orde der Dierenartsen (Franstaligen)', $authorization?->description->first());
        $this->assertSame('1992-09-01', $authorization?->period->begin?->toString());
    }

    private function authorizationMapper(): AuthorizationMapper
    {
        return new AuthorizationMapper();
    }
}
