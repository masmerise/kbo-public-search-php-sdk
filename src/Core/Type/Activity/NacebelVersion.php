<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Activity;

/** The NACEBEL version an activity code belongs to (Activity > Version, cookbook §2.1.2). */
enum NacebelVersion: string
{
    case Nacebel2003 = '2003';
    case Nacebel2008 = '2008';
    case Nacebel2025 = '2025';
}
