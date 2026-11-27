<?php declare(strict_types=1);

namespace KboPublicSearch\EnterpriseOrEstablishment;

use KboPublicSearch\Authentication\Failure\CouldNotAuthenticate;
use KboPublicSearch\Core\Failure\CouldNotCompleteRequest;
use KboPublicSearch\Core\Failure\RequestLimitReached;
use KboPublicSearch\Core\Failure\UnknownException;
use KboPublicSearch\Core\Http\KboConnector;
use KboPublicSearch\Core\Http\KboResource;
use KboPublicSearch\Core\Type\Parameter\LocationFilter;
use KboPublicSearch\Core\Type\Primitive\StreetName;
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\EnterpriseOrEstablishment\Reply\EnterpriseOrEstablishmentSearchReply;
use KboPublicSearch\EnterpriseOrEstablishment\SearchByActivity\Failure\CouldNotSearchEnterprisesOrEstablishmentsByActivity;
use KboPublicSearch\EnterpriseOrEstablishment\SearchByActivity\Request\ReadEnterpriseOrEstablishmentByActivityRequest;
use KboPublicSearch\EnterpriseOrEstablishment\SearchByAddress\Failure\CouldNotSearchEnterprisesOrEstablishmentsByAddress;
use KboPublicSearch\EnterpriseOrEstablishment\SearchByAddress\Request\ReadEnterpriseOrEstablishmentByAddressRequest;
use KboPublicSearch\EnterpriseOrEstablishment\Type\Mapping\EnterpriseOrEstablishmentMapper;
use KboPublicSearch\EnterpriseOrEstablishment\Type\Parameter\ActivityFilter;
use KboPublicSearch\EnterpriseOrEstablishment\Type\Parameter\EnterpriseOrEstablishmentFilter;
use KboPublicSearch\EnterpriseOrEstablishment\Type\Primitive\HouseNumber;

/** @internal */
final readonly class EnterpriseOrEstablishmentResource extends KboResource
{
    public function __construct(
        KboConnector $client,
        private EnterpriseOrEstablishmentMapper $enterpriseOrEstablishmentMapper = new EnterpriseOrEstablishmentMapper(),
    ) {
        parent::__construct($client);
    }

    /**
     * Search enterprises and establishment units by NACEBEL activity in a location (SOAP operation ReadEnterpriseOrEstablishmentByActivity).
     *
     * @param ActivityFilter $activityFilter 1 to 10 NACEBEL 2025 codes or prefixes.
     * @param LocationFilter $locationFilter Municipalities, a municipality and its neighbors, or zipcodes.
     * @param EnterpriseOrEstablishmentFilter|null $enterpriseOrEstablishmentFilter Natural persons, legal persons and/or establishment units; all by default.
     * @param bool $onlyActive Only active enterprises and establishment units.
     *
     * @throws CouldNotAuthenticate
     * @throws CouldNotCompleteRequest
     * @throws CouldNotSearchEnterprisesOrEstablishmentsByActivity
     * @throws RequestLimitReached
     * @throws UnknownException
     */
    public function searchByActivity(ActivityFilter $activityFilter, LocationFilter $locationFilter, ?EnterpriseOrEstablishmentFilter $enterpriseOrEstablishmentFilter = null, bool $onlyActive = true): EnterpriseOrEstablishmentSearchReply
    {
        $unwrapped = $this->sendSearch(
            new ReadEnterpriseOrEstablishmentByActivityRequest($activityFilter, $locationFilter, $enterpriseOrEstablishmentFilter ?? EnterpriseOrEstablishmentFilter::all(), $onlyActive),
            CouldNotSearchEnterprisesOrEstablishmentsByActivity::class,
        );

        return $this->reply(fn () => new EnterpriseOrEstablishmentSearchReply($unwrapped->context, $this->enterpriseOrEstablishmentMapper->toEnterprisesOrEstablishments($unwrapped->body)));
    }

    /**
     * Search enterprises and establishment units at an address (SOAP operation ReadEnterpriseOrEstablishmentByAddress).
     *
     * @param Zipcode $zipcode The zipcode.
     * @param StreetName $streetName The street name.
     * @param HouseNumber $houseNumber The house number.
     * @param bool $onlyActive Only active enterprises and establishment units.
     *
     * @throws CouldNotAuthenticate
     * @throws CouldNotCompleteRequest
     * @throws CouldNotSearchEnterprisesOrEstablishmentsByAddress
     * @throws RequestLimitReached
     * @throws UnknownException
     */
    public function searchByAddress(Zipcode $zipcode, StreetName $streetName, HouseNumber $houseNumber, bool $onlyActive = true): EnterpriseOrEstablishmentSearchReply
    {
        $unwrapped = $this->sendSearch(
            new ReadEnterpriseOrEstablishmentByAddressRequest($zipcode, $streetName, $houseNumber, $onlyActive),
            CouldNotSearchEnterprisesOrEstablishmentsByAddress::class,
        );

        return $this->reply(fn () => new EnterpriseOrEstablishmentSearchReply($unwrapped->context, $this->enterpriseOrEstablishmentMapper->toEnterprisesOrEstablishments($unwrapped->body)));
    }
}
