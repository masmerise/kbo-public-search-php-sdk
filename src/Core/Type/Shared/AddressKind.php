<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Shared;

/** TypeOfAddress codes (enumerated in the XSD); unknown codes map to null. */
enum AddressKind: string
{
    case RegisteredOffice = 'REGO';
    case NotAvailable = 'NOAD';
    case EstablishmentUnit = 'BAET';
    case BelgianBranch = 'ABBR';
}
