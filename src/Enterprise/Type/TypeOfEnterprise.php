<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

enum TypeOfEnterprise: string
{
    case LegalPerson = 'ELP';
    case NaturalPerson = 'EPP';
}
