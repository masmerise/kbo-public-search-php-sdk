<?php declare(strict_types=1);

namespace KboPublicSearch\Street\Type\Mapping;

use KboPublicSearch\Core\Type\Primitive\StreetName;
use KboPublicSearch\Core\Xml\Element;
use KboPublicSearch\Street\Type\StreetNames;

/** @internal */
final readonly class StreetNameMapper
{
    public function toStreetNames(Element $parent): StreetNames
    {
        return StreetNames::of(array_map(static fn (Element $name) => StreetName::fromString($name->text()), $parent->children('StreetName')));
    }
}
