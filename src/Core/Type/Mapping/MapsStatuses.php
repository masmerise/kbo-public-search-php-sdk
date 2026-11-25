<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Status\Status;
use KboPublicSearch\Core\Type\Status\StatusKind;
use KboPublicSearch\Core\Xml\Element;

/** @internal */
trait MapsStatuses
{
    use MapsLocalizedTexts;

    private function toStatus(Element $status): Status
    {
        $code = $status->textOf('Code');

        return new Status(
            code: $code,
            kind: StatusKind::tryFrom($code),
            description: $this->toLocalizedText($status),
        );
    }
}
