<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type;

/** Whether an enterprise is a legal or a physical person (datamodel.xsd Type, cookbook §2.1.2). */
enum TypeOfEnterprise: string
{
    case LegalPerson = 'ELP';
    case PhysicalPerson = 'EPP';
}
