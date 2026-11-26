<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Activity;

/** Whether an activity is a main, ancillary or secondary one (ActivityClassificationCodeType, enumerated in datamodel.xsd). */
enum ActivityClassificationCode: string
{
    case Main = 'MAIN';
    case Ancillary = 'ANCI';
    case Secondary = 'SECO';
}
