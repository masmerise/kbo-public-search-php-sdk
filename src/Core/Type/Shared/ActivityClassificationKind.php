<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Shared;

/** ActivityClassification codes (enumerated in the XSD). */
enum ActivityClassificationKind: string
{
    case Main = 'MAIN';
    case Ancillary = 'ANCI';
    case Secondary = 'SECO';
}
