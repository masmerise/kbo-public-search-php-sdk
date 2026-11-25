<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Mapping\MapsDenominations;
use KboPublicSearch\Core\Type\Shared\DenominationKind;
use KboPublicSearch\Localization\Language;
use KboPublicSearch\Tests\Support\Snippet;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class MapsDenominationsTest extends TestCase
{
    use MapsDenominations;

    #[Test]
    public function maps_all_languages(): void
    {
        // Act
        $denominations = $this->toDenominations(Snippet::load('shared/denominations'));

        // Assert
        $this->assertCount(6, $denominations);
        $this->assertSame('Federale Overheidsdienst Economie, KMO, Middenstand en Energie', $denominations->primary(Language::Dutch)?->name);
        $this->assertSame('de', $denominations->primary(Language::German)?->language);
        $this->assertSame('fr', $denominations->primary()?->language);
        $this->assertSame(DenominationKind::Abbreviation, $denominations[4]->kind);
        $this->assertSame('002', $denominations[4]->code);
        $this->assertSame('FOD Economie', $denominations[4]->name);
        $this->assertSame('2002-03-05', $denominations[4]->since?->toString());
    }

    #[Test]
    public function natural_person_names_have_no_language(): void
    {
        // Act
        $denominations = $this->toDenominations(Snippet::load('shared/denominations-natural-person'));

        // Assert
        $this->assertSame('Doe, John', $denominations->primary()?->name);
        $this->assertSame('', $denominations->primary()?->language);
        $this->assertNull($denominations->primary(Language::Dutch));
    }

    #[Test]
    public function unknown_code_keeps_the_raw_code(): void
    {
        // Act
        $denomination = $this->toDenominations(Snippet::load('shared/denominations-natural-person'))[1];

        // Assert
        $this->assertSame('009', $denomination->code);
        $this->assertNull($denomination->kind);
        $this->assertSame('Something new', $denomination->name);
    }
}
