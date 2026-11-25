<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Support;

use KboPublicSearch\Core\Http\Result;
use KboPublicSearch\Core\Http\Soap\Envelope;

/** Reads a recorded Saloon fixture directly, for mapper unit tests. */
final class Recording
{
    public static function result(string $fixture): Result
    {
        $recorded = json_decode(file_get_contents(__DIR__ . "/../Fixtures/Saloon/{$fixture}.json"), true, flags: JSON_THROW_ON_ERROR);

        return Envelope::unwrap($recorded['data']);
    }
}
