<?php declare(strict_types=1);

namespace KboPublicSearch\Street;

use KboPublicSearch\Authentication\Failure\CouldNotAuthenticate;
use KboPublicSearch\Core\Failure\CouldNotCompleteRequest;
use KboPublicSearch\Core\Failure\RequestLimitReached;
use KboPublicSearch\Core\Failure\UnknownException;
use KboPublicSearch\Core\Http\KboResource;
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\Street\Reply\StreetNameReply;
use KboPublicSearch\Street\Search\Failure\CouldNotSearchStreets;
use KboPublicSearch\Street\Search\Request\ReadStreetNameRequest;
use KboPublicSearch\Street\Type\Mapping\MapsStreetNames;
use KboPublicSearch\Street\Type\Primitive\PartOfStreetName;

/** @internal */
final readonly class StreetResource extends KboResource
{
    use MapsStreetNames;

    /**
     * Search the street names in a zipcode that contain a text (SOAP operation ReadStreetName).
     *
     * @param Zipcode $zipcode The zipcode.
     * @param PartOfStreetName $partOfStreetName The text the street names must contain.
     *
     * @throws CouldNotAuthenticate
     * @throws CouldNotCompleteRequest
     * @throws CouldNotSearchStreets
     * @throws RequestLimitReached
     * @throws UnknownException
     */
    public function search(Zipcode $zipcode, PartOfStreetName $partOfStreetName): StreetNameReply
    {
        $unwrapped = $this->searchFor(new ReadStreetNameRequest($zipcode, $partOfStreetName), CouldNotSearchStreets::class);

        return $this->reply(fn () => new StreetNameReply($unwrapped->context, $this->toStreetNames($unwrapped->body)));
    }
}
