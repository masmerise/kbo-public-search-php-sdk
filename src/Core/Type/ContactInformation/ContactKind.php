<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\ContactInformation;

/**
 * The documented ContactType codes.
 *
 * SDK convention: a *Kind enum lists the codes KBO documents. The raw code is always kept next to it as $code,
 * and an unknown code maps to a null kind.
 */
enum ContactKind: string
{
    case Phone = '001';
    case Fax = '002';
    case Website = '003';
    case Email = '004';
}
