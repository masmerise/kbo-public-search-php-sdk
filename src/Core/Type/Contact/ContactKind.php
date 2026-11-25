<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Contact;

/** The documented ContactType codes. */
enum ContactKind: string
{
    case Phone = '001';
    case Fax = '002';
    case Website = '003';
    case Email = '004';
}
