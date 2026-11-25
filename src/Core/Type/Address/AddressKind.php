<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Address;

/**
 * TypeOfAddress codes (enumerated in the XSD); unknown codes map to null.
 *
 * SDK convention: a *Kind enum lists the codes KBO documents. The raw code is always kept next to it as $code,
 * and an unknown code maps to a null kind.
 */
enum AddressKind: string
{
    case RegisteredOffice = 'REGO';
    case NotAvailable = 'NOAD';
    case EstablishmentUnit = 'BAET';
    case BelgianBranch = 'ABBR';
}
