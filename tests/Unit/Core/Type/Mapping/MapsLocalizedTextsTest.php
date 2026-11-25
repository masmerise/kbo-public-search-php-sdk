<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Mapping\MapsLocalizedTexts;
use KboPublicSearch\Core\Type\Shared\LocalizedText;
use KboPublicSearch\Localization\Language;
use KboPublicSearch\Tests\Support\Snippet;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class MapsLocalizedTextsTest extends TestCase
{
    use MapsLocalizedTexts;

    #[Test]
    public function keeps_every_language(): void
    {
        // Act
        $text = $this->toLocalizedText(Snippet::load('shared/localized-texts')->child('Status'));

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
        $text = LocalizedText::of([]);

        // Assert
        $this->assertNull($text->first());
        $this->assertSame('', (string) $text);
    }
}
