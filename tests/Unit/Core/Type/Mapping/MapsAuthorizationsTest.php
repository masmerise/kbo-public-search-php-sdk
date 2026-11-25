<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Mapping\MapsAuthorizations;
use KboPublicSearch\Tests\Support\Snippet;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class MapsAuthorizationsTest extends TestCase
{
    use MapsAuthorizations;

    #[Test]
    public function maps_code_description_and_period(): void
    {
        // Act
        $authorization = $this->toAuthorizations(Snippet::load('shared/authorizations'))->first();

        // Assert
        $this->assertSame('00504', $authorization?->code);
        $this->assertSame('Erkend door de Orde der Dierenartsen (Franstaligen)', $authorization?->description->first());
        $this->assertSame('1992-09-01', $authorization?->period->begin?->toString());
    }
}
