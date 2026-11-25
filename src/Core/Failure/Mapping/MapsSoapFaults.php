<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Failure\Mapping;

use KboPublicSearch\Authentication\Failure\CouldNotAuthenticate;
use KboPublicSearch\Authentication\Failure\MustAcceptTermsOfService;
use KboPublicSearch\Core\Failure\CouldNotCompleteRequest;
use KboPublicSearch\Core\Failure\KboPublicSearchException;
use KboPublicSearch\Core\Failure\RequestLimitReached;
use KboPublicSearch\Core\Http\Soap\Fault;

/** @internal The one place that knows KBO's fault wordings (cookbook §1.5); unknown wordings degrade gracefully. */
trait MapsSoapFaults
{
    private static function toException(Fault $fault): KboPublicSearchException
    {
        $string = strtolower($fault->string);
        $code = strtolower($fault->code);

        return match (true) {
            str_contains($string, 'accept the terms of service') => MustAcceptTermsOfService::because($fault->string),
            str_contains($string, 'security token could not be authenticated'),
            // WSS4J's generic answer, which is what KBO actually sends for wrong credentials.
            str_contains($string, 'security error was encountered'),
            str_ends_with($code, ':securityerror'),
            str_ends_with($code, ':failedauthentication') => CouldNotAuthenticate::because($fault->string),
            str_contains($string, 'quota exceeded'),
            str_contains($string, 'balance is empty') => RequestLimitReached::because($fault->string),
            default => new CouldNotCompleteRequest($fault->code, $fault->string),
        };
    }
}
