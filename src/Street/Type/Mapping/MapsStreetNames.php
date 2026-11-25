<?php declare(strict_types=1);

namespace KboPublicSearch\Street\Type\Mapping;

use KboPublicSearch\Core\Xml\Element;
use KboPublicSearch\Street\Type\StreetNames;

/** @internal */
trait MapsStreetNames
{
    private function toStreetNames(Element $parent): StreetNames
    {
        return StreetNames::of(array_map(static fn (Element $name) => $name->text(), $parent->children('StreetName')));
    }
}
