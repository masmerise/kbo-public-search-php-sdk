<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Shared;

/** A code with its description, e.g. a technical creation reason. */
final readonly class CodedValue
{
    /** @internal */
    public function __construct(
        public string $code,
        public LocalizedText $description,
    ) {}
}
