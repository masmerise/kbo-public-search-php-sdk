<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Shared;

use KboPublicSearch\Core\Type\Collection;
use KboPublicSearch\Localization\Language;

/**
 * Denominations, in every language KBO holds, regardless of the requested languages.
 *
 * @extends Collection<Denomination>
 */
final readonly class Denominations extends Collection
{
    /** The first name (code 001), in the given language when one is given. */
    public function primary(?Language $language = null): ?Denomination
    {
        foreach ($this->items as $denomination) {
            if ($denomination->kind === DenominationKind::Name && ($language === null || $denomination->language === $language->value)) {
                return $denomination;
            }
        }

        return null;
    }
}
