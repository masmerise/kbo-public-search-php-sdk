<?php declare(strict_types=1);

namespace KboPublicSearch\CodeList\Type;

/** Whether an authorization search code is for enterprises or establishment units (KeyValuePair EntityKind, cookbook §2.14.1). */
enum EntityKind: string
{
    case Enterprise = 'enterprise';
    case Establishment = 'establishment';
}
