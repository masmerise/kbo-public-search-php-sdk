<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Address;

/** The type of an address (TypeOfAddressCodeType, enumerated in datamodel.xsd; cookbook §2.1.2). */
enum TypeOfAddressCode: string
{
    case RegisteredOffice = 'REGO';
    case NotAvailable = 'NOAD';
    case EstablishmentUnit = 'BAET';
    case BelgianBranch = 'ABBR';
}
