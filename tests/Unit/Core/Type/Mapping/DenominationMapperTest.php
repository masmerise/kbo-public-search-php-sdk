<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Denomination\DenominationCode;
use KboPublicSearch\Core\Type\Mapping\DenominationMapper;
use KboPublicSearch\Localization\Language;
use KboPublicSearch\Tests\Support\Snippet;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ValueError;

final class DenominationMapperTest extends TestCase
{
    #[Test]
    public function maps_all_languages(): void
    {
        // Act
        $denominations = $this->denominationMapper()->toDenominations(Snippet::load('shared/denominations'));

        // Assert
        $this->assertCount(6, $denominations);
        $this->assertSame('Federale Overheidsdienst Economie, KMO, Middenstand en Energie', $denominations->primary(Language::Dutch)?->name);
        $this->assertSame(Language::German, $denominations->primary(Language::German)?->language);
        $this->assertSame(Language::French, $denominations->primary()?->language);
        $this->assertSame(DenominationCode::Abbreviation, $denominations[4]->code);
        $this->assertSame('FOD Economie', $denominations[4]->name);
        $this->assertSame('2002-03-05', $denominations[4]->validityPeriod?->begin?->toString());
    }

    #[Test]
    public function natural_person_names_have_no_language(): void
    {
        // Act
        $denominations = $this->denominationMapper()->toDenominations(Snippet::load('shared/denominations-natural-person'));

        // Assert
        $this->assertSame('Doe, John', $denominations->primary()?->name);
        $this->assertNull($denominations->primary()?->language);
        $this->assertNull($denominations->primary(Language::Dutch));
    }

    #[Test]
    public function unknown_code_fails(): void
    {
        // Assert
        $this->expectException(ValueError::class);

        // Act
        $this->denominationMapper()->toDenominations(Snippet::load('shared/unknown/denomination'));
    }

    private function denominationMapper(): DenominationMapper
    {
        return new DenominationMapper();
    }
}
