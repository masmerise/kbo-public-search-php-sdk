<?php declare(strict_types=1);

namespace KboPublicSearch\Entity;

use KboPublicSearch\Authentication\Failure\CouldNotAuthenticate;
use KboPublicSearch\Core\Failure\CouldNotCompleteRequest;
use KboPublicSearch\Core\Failure\RequestLimitReached;
use KboPublicSearch\Core\Failure\UnknownException;
use KboPublicSearch\Core\Http\KboResource;
use KboPublicSearch\Core\Type\Parameter\LocationFilter;
use KboPublicSearch\Core\Type\Primitive\HouseNumber;
use KboPublicSearch\Core\Type\Primitive\StreetName;
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\Entity\Reply\EntitySearchReply;
use KboPublicSearch\Entity\SearchByActivity\Failure\CouldNotSearchEntitiesByActivity;
use KboPublicSearch\Entity\SearchByActivity\Request\ReadEnterpriseOrEstablishmentByActivityRequest;
use KboPublicSearch\Entity\SearchByAddress\Failure\CouldNotSearchEntitiesByAddress;
use KboPublicSearch\Entity\SearchByAddress\Request\ReadEnterpriseOrEstablishmentByAddressRequest;
use KboPublicSearch\Entity\Type\ActivityCodes;
use KboPublicSearch\Entity\Type\EntityKinds;
use KboPublicSearch\Entity\Type\Mapping\MapsEntities;

/** @internal */
final readonly class EntityResource extends KboResource
{
    use MapsEntities;

    /**
     * Search enterprises and establishment units by NACEBEL activity in a location (SOAP operation ReadEnterpriseOrEstablishmentByActivity).
     *
     * @param ActivityCodes $activities 1 to 10 NACEBEL 2025 codes or prefixes.
     * @param LocationFilter $location Municipalities, a municipality and its neighbours, or zipcodes.
     * @param EntityKinds|null $kinds Natural persons, legal persons and/or establishment units; all by default.
     * @param bool $onlyActive Only active entities.
     *
     * @throws CouldNotAuthenticate
     * @throws CouldNotCompleteRequest
     * @throws CouldNotSearchEntitiesByActivity
     * @throws RequestLimitReached
     * @throws UnknownException
     */
    public function searchByActivity(ActivityCodes $activities, LocationFilter $location, ?EntityKinds $kinds = null, bool $onlyActive = true): EntitySearchReply
    {
        $unwrapped = $this->searchFor(
            new ReadEnterpriseOrEstablishmentByActivityRequest($activities, $location, $kinds ?? EntityKinds::all(), $onlyActive),
            CouldNotSearchEntitiesByActivity::class,
        );

        return $this->reply(fn () => new EntitySearchReply($unwrapped->context, $this->toEntities($unwrapped->body)));
    }

    /**
     * Search enterprises and establishment units at an address (SOAP operation ReadEnterpriseOrEstablishmentByAddress).
     *
     * @param Zipcode $zipcode The zipcode.
     * @param StreetName $street The street name.
     * @param HouseNumber $houseNumber The house number.
     * @param bool $onlyActive Only active entities.
     *
     * @throws CouldNotAuthenticate
     * @throws CouldNotCompleteRequest
     * @throws CouldNotSearchEntitiesByAddress
     * @throws RequestLimitReached
     * @throws UnknownException
     */
    public function searchByAddress(Zipcode $zipcode, StreetName $street, HouseNumber $houseNumber, bool $onlyActive = true): EntitySearchReply
    {
        $unwrapped = $this->searchFor(
            new ReadEnterpriseOrEstablishmentByAddressRequest($zipcode, $street, $houseNumber, $onlyActive),
            CouldNotSearchEntitiesByAddress::class,
        );

        return $this->reply(fn () => new EntitySearchReply($unwrapped->context, $this->toEntities($unwrapped->body)));
    }
}
