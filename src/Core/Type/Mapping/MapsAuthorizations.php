<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Shared\Authorization;
use KboPublicSearch\Core\Type\Shared\Authorizations;
use KboPublicSearch\Core\Xml\Element;

/** @internal */
trait MapsAuthorizations
{
    use MapsLocalizedTexts;
    use MapsPeriods;

    private function toAuthorization(Element $authorization): Authorization
    {
        return new Authorization(
            code: $authorization->textOf('Code'),
            description: $this->toLocalizedText($authorization),
            period: $this->toRequiredPeriod($authorization, 'Period'),
        );
    }

    private function toAuthorizations(Element $parent): Authorizations
    {
        return Authorizations::of(array_map($this->toAuthorization(...), $parent->children('Authorization')));
    }
}
