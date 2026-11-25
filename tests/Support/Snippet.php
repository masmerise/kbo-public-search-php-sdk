<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Support;

use KboPublicSearch\Core\Xml\Element;
use KboPublicSearch\Core\Xml\Reader;

/** Loads a hand-written cookbook fixture from tests/Fixtures/Cookbook. */
final class Snippet
{
    public static function load(string $name): Element
    {
        return Reader::fromString(file_get_contents(__DIR__ . "/../Fixtures/Cookbook/{$name}.xml"));
    }
}
