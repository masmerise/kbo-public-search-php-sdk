<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Parameter;

/** The level of detail of establishment replies ("TypeOfResult"). */
enum TypeOfResult: string
{
    case Short = 'short';
    case Long = 'long';
}
