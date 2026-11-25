<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Status;

/**
 * The documented status codes (cookbook §2.1.2).
 *
 * SDK convention: a *Kind enum lists the codes KBO documents. The raw code is always kept next to it as $code,
 * and an unknown code maps to a null kind.
 */
enum StatusKind: string
{
    case Active = 'AC';
    case Announced = 'BK';
    case Stopped = 'ST';
    case JuridicallyCreated = 'JU';
}
