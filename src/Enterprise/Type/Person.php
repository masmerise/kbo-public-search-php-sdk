<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

/** A physical person exercising a function. */
final readonly class Person
{
    /** @internal */
    public function __construct(
        public string $surname,
        public string $givenName,
    ) {}
}
