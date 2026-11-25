<?php declare(strict_types=1);

namespace KboPublicSearch\CodeList\Type;

use KboPublicSearch\Core\Type\Shared\LocalizedText;

final readonly class CodeListItem
{
    /**
     * @internal
     *
     * @param list<KeyValuePair> $keyValuePairs
     */
    public function __construct(
        public string $code,
        public LocalizedText $description,
        public array $keyValuePairs,
    ) {}

    /** The value of the first pair with this key. */
    public function value(string $key): ?string
    {
        foreach ($this->keyValuePairs as $pair) {
            if ($pair->key === $key) {
                return $pair->value;
            }
        }

        return null;
    }
}
