<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

use KboPublicSearch\Core\Type\Collection;

/**
 * Enterprises found by a search (at most 200).
 *
 * @extends Collection<EnterpriseSummary>
 */
final readonly class EnterpriseSummaries extends Collection {}
