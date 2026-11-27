<?php declare(strict_types=1);

namespace KboPublicSearch\Authentication\Failure;

use KboPublicSearch\Core\Failure\KboPublicSearchException;

/** KBO refused access: catch this for every authentication failure, or one of its subclasses for a specific one. */
abstract class CouldNotAuthenticate extends KboPublicSearchException {}
