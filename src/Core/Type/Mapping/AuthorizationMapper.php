<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Authorization\Authorization;
use KboPublicSearch\Core\Type\Authorization\Authorizations;
use KboPublicSearch\Core\Type\Primitive\AuthorizationCode;
use KboPublicSearch\Core\Xml\Element;

/** @internal */
final readonly class AuthorizationMapper
{
    public function __construct(
        private DescriptionMapper $descriptionMapper = new DescriptionMapper(),
        private PeriodMapper $periodMapper = new PeriodMapper(),
    ) {}

    private function toAuthorization(Element $authorization): Authorization
    {
        return new Authorization(
            code: AuthorizationCode::fromString($authorization->textOf('Code')),
            description: $this->descriptionMapper->toDescription($authorization),
            period: $this->periodMapper->toRequiredPeriod($authorization, 'Period'),
        );
    }

    public function toAuthorizations(Element $parent): Authorizations
    {
        return Authorizations::of(array_map($this->toAuthorization(...), $parent->children('Authorization')));
    }
}
