<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Shared;

/** An authorization ("toelating"). */
final readonly class Authorization
{
    /** @internal */
    public function __construct(
        public string $code,
        public LocalizedText $description,
        public Period $period,
    ) {}
}
