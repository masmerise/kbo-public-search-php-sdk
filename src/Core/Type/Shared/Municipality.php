<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Shared;

/** A municipality. */
final readonly class Municipality
{
    /** @internal */
    public function __construct(
        /** The NIS code; null for foreign addresses. */
        public ?string $code,
        public LocalizedText $name,
    ) {}
}
