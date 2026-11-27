<?php declare(strict_types=1);

namespace KboPublicSearch\Street\Type;

use KboPublicSearch\Core\Type\Collection;
use KboPublicSearch\Core\Type\Primitive\StreetName;

/**
 * Street names, as KBO writes them (uppercase). Each can be passed on to an address search.
 *
 * @extends Collection<StreetName>
 */
final readonly class StreetNames extends Collection {}
