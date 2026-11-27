<?php declare(strict_types=1);

namespace KboPublicSearch\Environments;

/** @internal */
enum Environment
{
    case Production;
    case Test;

    public function url(): string
    {
        return match ($this) {
            self::Test => 'https://kbopub-acc.economie.fgov.be/kbopubws180000/services/wsKBOPub',
            default => 'https://kbopub.economie.fgov.be/kbopubws180000/services/wsKBOPub',
        };
    }
}
