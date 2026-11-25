<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Denomination;

/** Denomination codes (enumerated in the XSD); unknown codes map to null. */
enum DenominationKind: string
{
    case Name = '001';
    case Abbreviation = '002';
    case CommercialName = '003';
    case BranchName = '004';
}
