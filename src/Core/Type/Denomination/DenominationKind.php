<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Denomination;

/**
 * Denomination codes (enumerated in the XSD); unknown codes map to null.
 *
 * SDK convention: a *Kind enum lists the codes KBO documents. The raw code is always kept next to it as $code,
 * and an unknown code maps to a null kind.
 */
enum DenominationKind: string
{
    case Name = '001';
    case Abbreviation = '002';
    case CommercialName = '003';
    case BranchName = '004';
}
