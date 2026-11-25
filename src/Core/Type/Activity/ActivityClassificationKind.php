<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Activity;

/**
 * ActivityClassification codes (enumerated in the XSD); unknown codes map to null.
 *
 * SDK convention: a *Kind enum lists the codes KBO documents. The raw code is always kept next to it as $code,
 * and an unknown code maps to a null kind.
 */
enum ActivityClassificationKind: string
{
    case Main = 'MAIN';
    case Ancillary = 'ANCI';
    case Secondary = 'SECO';
}
