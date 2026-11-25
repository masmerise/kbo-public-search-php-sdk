<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Status;

/** The documented status codes (cookbook §2.1.2). */
enum StatusKind: string
{
    case Active = 'AC';
    case Announced = 'BK';
    case Stopped = 'ST';
    case JuridicallyCreated = 'JU';
}
