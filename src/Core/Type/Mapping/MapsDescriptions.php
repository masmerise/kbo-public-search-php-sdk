<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Description;
use KboPublicSearch\Core\Xml\Element;

/** @internal */
trait MapsDescriptions
{
    /** Reads the repeated {Value, Language} elements named $element (Description, PrimaryDenomination, ...). */
    private function toDescription(Element $parent, string $element = 'Description'): Description
    {
        $texts = [];

        foreach ($parent->children($element) as $description) {
            $texts[$description->optionalText('Language') ?? ''] ??= $description->optionalText('Value') ?? '';
        }

        return Description::of($texts);
    }
}
