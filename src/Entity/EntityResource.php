<?php declare(strict_types=1);

namespace KboPublicSearch\Entity;

use KboPublicSearch\Authentication\Failure\CouldNotAuthenticate;
use KboPublicSearch\Core\Failure\CouldNotCompleteRequest;
use KboPublicSearch\Core\Failure\RequestLimitReached;
use KboPublicSearch\Core\Failure\UnknownException;
use KboPublicSearch\Core\Http\KboConnector;
use KboPublicSearch\Core\Http\KboResource;
use KboPublicSearch\Core\Type\Parameter\LocationFilter;
use KboPublicSearch\Core\Type\Primitive\StreetName;
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\Entity\Reply\EntitySearchReply;
use KboPublicSearch\Entity\SearchByActivity\Failure\CouldNotSearchEntitiesByActivity;
use KboPublicSearch\Entity\SearchByActivity\Request\ReadEnterpriseOrEstablishmentByActivityRequest;
use KboPublicSearch\Entity\SearchByAddress\Failure\CouldNotSearchEntitiesByAddress;
use KboPublicSearch\Entity\SearchByAddress\Request\ReadEnterpriseOrEstablishmentByAddressRequest;
use KboPublicSearch\Entity\Type\ActivityFilter;
use KboPublicSearch\Entity\Type\EntityFilter;
use KboPublicSearch\Entity\Type\Mapping\EntityMapper;
use KboPublicSearch\Entity\Type\Primitive\HouseNumber;

/** @internal */
final readonly class EntityResource extends KboResource
{
    public function __construct(
        KboConnector $client,
        private EntityMapper $entityMapper = new EntityMapper(),
    ) {
        parent::__construct($client);
    }

    /**
     * Search enterprises and establishment units by NACEBEL activity in a location (SOAP operation ReadEnterpriseOrEstablishmentByActivity).
     *
     * @param ActivityFilter $activityFilter 1 to 10 NACEBEL 2025 codes or prefixes.
     * @param LocationFilter $locationFilter Municipalities, a municipality and its neighbours, or zipcodes.
     * @param EntityFilter|null $entityFilter Natural persons, legal persons and/or establishment units; all by default.
     * @param bool $onlyActive Only active entities.
     *
     * @throws CouldNotAuthenticate
     * @throws CouldNotCompleteRequest
     * @throws CouldNotSearchEntitiesByActivity
     * @throws RequestLimitReached
     * @throws UnknownException
     */
    public function searchByActivity(ActivityFilter $activityFilter, LocationFilter $locationFilter, ?EntityFilter $entityFilter = null, bool $onlyActive = true): EntitySearchReply
    {
        $unwrapped = $this->searchFor(
            new ReadEnterpriseOrEstablishmentByActivityRequest($activityFilter, $locationFilter, $entityFilter ?? EntityFilter::all(), $onlyActive),
            CouldNotSearchEntitiesByActivity::class,
        );

        return $this->reply(fn () => new EntitySearchReply($unwrapped->context, $this->entityMapper->toEntities($unwrapped->body)));
    }

    /**
     * Search enterprises and establishment units at an address (SOAP operation ReadEnterpriseOrEstablishmentByAddress).
     *
     * @param Zipcode $zipcode The zipcode.
     * @param StreetName $streetName The street name.
     * @param HouseNumber $houseNumber The house number.
     * @param bool $onlyActive Only active entities.
     *
     * @throws CouldNotAuthenticate
     * @throws CouldNotCompleteRequest
     * @throws CouldNotSearchEntitiesByAddress
     * @throws RequestLimitReached
     * @throws UnknownException
     */
    public function searchByAddress(Zipcode $zipcode, StreetName $streetName, HouseNumber $houseNumber, bool $onlyActive = true): EntitySearchReply
    {
        $unwrapped = $this->searchFor(
            new ReadEnterpriseOrEstablishmentByAddressRequest($zipcode, $streetName, $houseNumber, $onlyActive),
            CouldNotSearchEntitiesByAddress::class,
        );

        return $this->reply(fn () => new EntitySearchReply($unwrapped->context, $this->entityMapper->toEntities($unwrapped->body)));
    }
}
