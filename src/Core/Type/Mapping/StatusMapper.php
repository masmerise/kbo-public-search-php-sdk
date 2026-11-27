<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Status\Status;
use KboPublicSearch\Core\Type\Status\StatusCode;
use KboPublicSearch\Core\Xml\Element;

/** @internal */
final readonly class StatusMapper
{
    public function __construct(
        private DescriptionMapper $descriptionMapper = new DescriptionMapper(),
    ) {}

    public function toStatus(Element $status): Status
    {
        return new Status(
            code: StatusCode::from($status->textOf('Code')),
            description: $this->descriptionMapper->toDescription($status),
        );
    }
}
