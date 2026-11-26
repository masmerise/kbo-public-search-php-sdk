<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Description;
use KboPublicSearch\Core\Type\Mapping\DescriptionMapper;
use KboPublicSearch\Localization\Language;
use KboPublicSearch\Tests\Support\Snippet;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class DescriptionMapperTest extends TestCase
{
    #[Test]
    public function keeps_every_language(): void
    {
        // Act
        $text = $this->descriptionMapper()->toDescription(Snippet::load('shared/localized-texts')->child('Status'));

        // Assert
        $this->assertSame('Actif', $text->in(Language::French));
        $this->assertSame('Actief', $text->first());
        $this->assertNull($text->in(Language::German));
        $this->assertSame(['nl' => 'Actief', 'fr' => 'Actif'], $text->all());
        $this->assertSame('Actief', (string) $text);
    }

    #[Test]
    public function an_empty_text_is_empty(): void
    {
        // Act
        $text = Description::of([]);

        // Assert
        $this->assertNull($text->first());
        $this->assertSame('', (string) $text);
    }

    private function descriptionMapper(): DescriptionMapper
    {
        return new DescriptionMapper();
    }
}
