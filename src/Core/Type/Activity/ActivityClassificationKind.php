<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Activity;

/** ActivityClassification codes (enumerated in the XSD); unknown codes map to null. */
enum ActivityClassificationKind: string
{
    case Main = 'MAIN';
    case Ancillary = 'ANCI';
    case Secondary = 'SECO';
}
