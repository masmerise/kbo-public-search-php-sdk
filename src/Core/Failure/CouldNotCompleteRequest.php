<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Failure;

final class CouldNotCompleteRequest extends KboPublicSearchException
{
    public function __construct(
        public readonly string $faultCode,
        public readonly string $faultString,
    ) {
        parent::__construct($faultString);
    }
}
