<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Shared\LocalizedText;
use KboPublicSearch\Core\Xml\Element;

/** @internal */
trait MapsLocalizedTexts
{
    /** Reads the repeated {Value, Language} elements named $element (Description, PrimaryDenomination, ...). */
    private function toLocalizedText(Element $parent, string $element = 'Description'): LocalizedText
    {
        $texts = [];

        foreach ($parent->children($element) as $description) {
            $texts[$description->optionalText('Language') ?? ''] ??= $description->optionalText('Value') ?? '';
        }

        return LocalizedText::of($texts);
    }
}
