<?php declare(strict_types=1);

namespace KboPublicSearch\Localization;

enum Language: string
{
    case Dutch = 'nl';
    case French = 'fr';

    /** Only appears in replies (denominations); it cannot be requested. */
    case German = 'de';
    /** Only appears in replies (denominations); it cannot be requested. */
    case English = 'en';
}
