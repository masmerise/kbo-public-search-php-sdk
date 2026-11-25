<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise;

use KboPublicSearch\Authentication\Failure\CouldNotAuthenticate;
use KboPublicSearch\Core\Failure\CouldNotCompleteRequest;
use KboPublicSearch\Core\Failure\RequestLimitReached;
use KboPublicSearch\Core\Failure\UnknownException;
use KboPublicSearch\Core\Http\KboResource;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Enterprise\Find\Failure\CouldNotFindEnterprise;
use KboPublicSearch\Enterprise\Find\Request\ReadEnterpriseRequest;
use KboPublicSearch\Enterprise\Reply\EnterpriseReply;
use KboPublicSearch\Enterprise\Type\Mapping\MapsEnterprises;

/** @internal */
final readonly class EnterpriseResource extends KboResource
{
    use MapsEnterprises;

    /**
     * Read an enterprise by its number (SOAP operation ReadEnterprise).
     *
     * A closed number answers with the enterprise that replaced it.
     *
     * @param EnterpriseNumber $number The enterprise number.
     *
     * @throws CouldNotAuthenticate
     * @throws CouldNotCompleteRequest
     * @throws CouldNotFindEnterprise when the number does not exist, is canceled or is not active yet
     * @throws RequestLimitReached
     * @throws UnknownException
     */
    public function find(EnterpriseNumber $number): EnterpriseReply
    {
        $result = $this->lookup(new ReadEnterpriseRequest($number), CouldNotFindEnterprise::class);

        return $this->reply(fn () => new EnterpriseReply(
            $result->context,
            $this->toEnterprise($result->body->child('Enterprise')),
        ));
    }
}
