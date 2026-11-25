<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Http\Soap;

/** @internal */
final readonly class Fault
{
    public function __construct(
        public string $code,
        public string $string,
    ) {}
}
