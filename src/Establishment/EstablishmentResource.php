<?php declare(strict_types=1);

namespace KboPublicSearch\Establishment;

use KboPublicSearch\Authentication\Failure\CouldNotAuthenticate;
use KboPublicSearch\Core\Failure\CouldNotCompleteRequest;
use KboPublicSearch\Core\Failure\RequestLimitReached;
use KboPublicSearch\Core\Failure\UnknownException;
use KboPublicSearch\Core\Failure\ValidationException;
use KboPublicSearch\Core\Http\KboConnector;
use KboPublicSearch\Core\Http\KboResource;
use KboPublicSearch\Core\Http\Request;
use KboPublicSearch\Core\Type\Parameter\LocationFilter;
use KboPublicSearch\Core\Type\Parameter\Phonemes;
use KboPublicSearch\Core\Type\Primitive\AuthorizationSearchCode;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Core\Type\Primitive\EstablishmentNumber;
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\Establishment\Find\Failure\CouldNotFindEstablishment;
use KboPublicSearch\Establishment\Find\Request\ReadEstablishmentByEstablishmentNumberRequest;
use KboPublicSearch\Establishment\OfEnterprise\Failure\CouldNotGetEstablishmentsOfEnterprise;
use KboPublicSearch\Establishment\OfEnterprise\Request\ReadEstablishmentByEnterpriseNumberRequest;
use KboPublicSearch\Establishment\Reply\EstablishmentReply;
use KboPublicSearch\Establishment\Reply\EstablishmentSearchReply;
use KboPublicSearch\Establishment\Reply\EstablishmentsOfEnterpriseReply;
use KboPublicSearch\Establishment\SearchByAuthorization\Failure\CouldNotSearchEstablishmentsByAuthorization;
use KboPublicSearch\Establishment\SearchByAuthorization\Request\ReadEstablishmentByAuthorizationRequest;
use KboPublicSearch\Establishment\SearchByName\Failure\CouldNotSearchEstablishmentsByName;
use KboPublicSearch\Establishment\SearchByName\Request\ReadEstablishmentByNameExactMatchRequest;
use KboPublicSearch\Establishment\SearchByPhoneme\Failure\CouldNotSearchEstablishmentsByPhoneme;
use KboPublicSearch\Establishment\SearchByPhoneme\Request\ReadEstablishmentByPhonemeRequest;
use KboPublicSearch\Establishment\Type\Mapping\EstablishmentMapper;
use KboPublicSearch\Establishment\Type\Parameter\TypeOfResult;
use KboPublicSearch\Establishment\Type\Primitive\EstablishmentName;

/** @internal */
final readonly class EstablishmentResource extends KboResource
{
    public function __construct(
        KboConnector $client,
        private EstablishmentMapper $establishmentMapper = new EstablishmentMapper(),
    ) {
        parent::__construct($client);
    }

    /**
     * Read an establishment unit by its number (SOAP operation ReadEstablishmentByEstablishmentNumber).
     *
     * @param EstablishmentNumber $establishmentNumber The establishment unit number.
     * @param TypeOfResult $typeOfResult Short or long information.
     *
     * @throws CouldNotAuthenticate
     * @throws CouldNotCompleteRequest
     * @throws CouldNotFindEstablishment when the number does not exist, is canceled or is not active yet
     * @throws RequestLimitReached
     * @throws UnknownException
     */
    public function find(EstablishmentNumber $establishmentNumber, TypeOfResult $typeOfResult = TypeOfResult::Short): EstablishmentReply
    {
        $unwrapped = $this->sendLookup(new ReadEstablishmentByEstablishmentNumberRequest($establishmentNumber, $typeOfResult), CouldNotFindEstablishment::class);

        return $this->reply(fn () => new EstablishmentReply(
            $unwrapped->context,
            $this->establishmentMapper->toEstablishment($unwrapped->body->child('Establishment')),
        ));
    }

    /**
     * Read every active establishment unit of an enterprise (SOAP operation ReadEstablishmentByEnterpriseNumber).
     *
     * The list is not limited; prefer TypeOfResult::Short for enterprises with many establishments. An enterprise without
     * active establishment units yields an empty list.
     *
     * @param EnterpriseNumber $enterpriseNumber The enterprise number.
     * @param TypeOfResult $typeOfResult Short or long information.
     *
     * @throws CouldNotAuthenticate
     * @throws CouldNotCompleteRequest
     * @throws CouldNotGetEstablishmentsOfEnterprise when the enterprise number is invalid, does not exist, is canceled or is not active yet
     * @throws RequestLimitReached
     * @throws UnknownException
     */
    public function ofEnterprise(EnterpriseNumber $enterpriseNumber, TypeOfResult $typeOfResult = TypeOfResult::Short): EstablishmentsOfEnterpriseReply
    {
        $unwrapped = $this->sendList(new ReadEstablishmentByEnterpriseNumberRequest($enterpriseNumber, $typeOfResult), CouldNotGetEstablishmentsOfEnterprise::class);

        return $this->reply(fn () => new EstablishmentsOfEnterpriseReply($unwrapped->context, $this->establishmentMapper->toEstablishments($unwrapped->body)));
    }

    /**
     * Search establishment units holding an authorization in a location (SOAP operation ReadEstablishmentByAuthorization).
     *
     * @param AuthorizationSearchCode $authorizationFilter An AUTHORIZATION_SEARCH_CODE, see codeLists->authorizationSearchCodes().
     * @param LocationFilter $locationFilter Municipalities, a municipality and its neighbors, or zipcodes.
     * @param bool $onlyActive Only active establishment units.
     *
     * @throws CouldNotAuthenticate
     * @throws CouldNotCompleteRequest
     * @throws CouldNotSearchEstablishmentsByAuthorization
     * @throws RequestLimitReached
     * @throws UnknownException
     */
    public function searchByAuthorization(AuthorizationSearchCode $authorizationFilter, LocationFilter $locationFilter, bool $onlyActive = true): EstablishmentSearchReply
    {
        return $this->searchEstablishments(
            new ReadEstablishmentByAuthorizationRequest($authorizationFilter, $locationFilter, $onlyActive),
            CouldNotSearchEstablishmentsByAuthorization::class,
        );
    }

    /**
     * Search establishment units by their exact name (SOAP operation ReadEstablishmentByNameExactMatch).
     *
     * @param EstablishmentName $establishmentName The exact name.
     * @param Zipcode|null $zipcode Only establishment units with this zipcode.
     * @param bool $onlyActive Only active establishment units.
     *
     * @throws CouldNotAuthenticate
     * @throws CouldNotCompleteRequest
     * @throws CouldNotSearchEstablishmentsByName
     * @throws RequestLimitReached
     * @throws UnknownException
     */
    public function searchByName(EstablishmentName $establishmentName, ?Zipcode $zipcode = null, bool $onlyActive = true): EstablishmentSearchReply
    {
        return $this->searchEstablishments(
            new ReadEstablishmentByNameExactMatchRequest($establishmentName, $zipcode, $onlyActive),
            CouldNotSearchEstablishmentsByName::class,
        );
    }

    /**
     * Search establishment units phonetically by name (SOAP operation ReadEstablishmentByPhoneme).
     *
     * @param Phonemes $phonemes 1 to 10 words.
     * @param Zipcode|null $zipcode Only establishment units with this zipcode.
     * @param bool $onlyActive Only active establishment units.
     *
     * @throws CouldNotAuthenticate
     * @throws CouldNotCompleteRequest
     * @throws CouldNotSearchEstablishmentsByPhoneme
     * @throws RequestLimitReached
     * @throws UnknownException
     */
    public function searchByPhoneme(Phonemes $phonemes, ?Zipcode $zipcode = null, bool $onlyActive = true): EstablishmentSearchReply
    {
        return $this->searchEstablishments(
            new ReadEstablishmentByPhonemeRequest($phonemes, $zipcode, $onlyActive),
            CouldNotSearchEstablishmentsByPhoneme::class,
        );
    }

    /** @param class-string<ValidationException> $onFailure */
    private function searchEstablishments(Request $request, string $onFailure): EstablishmentSearchReply
    {
        $unwrapped = $this->sendSearch($request, $onFailure);

        return $this->reply(fn () => new EstablishmentSearchReply($unwrapped->context, $this->establishmentMapper->toEstablishments($unwrapped->body)));
    }
}
