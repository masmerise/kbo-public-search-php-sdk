<?php declare(strict_types=1);

namespace KboPublicSearch\CodeList;

use KboPublicSearch\Authentication\Failure\CouldNotAuthenticate;
use KboPublicSearch\CodeList\Read\Failure\CouldNotReadCodeList;
use KboPublicSearch\CodeList\Read\Request\ReadCodeListRequest;
use KboPublicSearch\CodeList\Reply\AuthorizationSearchCodesReply;
use KboPublicSearch\CodeList\Reply\MetaCodeListReply;
use KboPublicSearch\CodeList\Type\CodeListId;
use KboPublicSearch\CodeList\Type\Mapping\CodeListMapper;
use KboPublicSearch\Core\Failure\CouldNotCompleteRequest;
use KboPublicSearch\Core\Failure\RequestLimitReached;
use KboPublicSearch\Core\Failure\UnknownException;
use KboPublicSearch\Core\Http\KboConnector;
use KboPublicSearch\Core\Http\KboResource;

/** @internal */
final readonly class CodeListResource extends KboResource
{
    public function __construct(
        KboConnector $client,
        private CodeListMapper $codeListMapper = new CodeListMapper(),
    ) {
        parent::__construct($client);
    }

    /**
     * Read the AUTHORIZATION_SEARCH_CODE code list (SOAP operation ReadCodeList). Free: it does not count towards the daily quota.
     *
     * @throws CouldNotAuthenticate
     * @throws CouldNotCompleteRequest
     * @throws CouldNotReadCodeList when KBO does not know the list (10003)
     * @throws RequestLimitReached
     * @throws UnknownException also when an item has an EntityKind this SDK does not know
     */
    public function authorizationSearchCodes(): AuthorizationSearchCodesReply
    {
        $unwrapped = $this->sendLookup(new ReadCodeListRequest(CodeListId::AuthorizationSearchCode), CouldNotReadCodeList::class);

        return $this->reply(fn () => new AuthorizationSearchCodesReply($unwrapped->context, $this->codeListMapper->toAuthorizationSearchCodes($unwrapped->body)));
    }

    /**
     * Read the META_CODE_LIST code list: the code lists KBO serves (SOAP operation ReadCodeList). Free: it does not count towards the daily quota.
     *
     * @throws CouldNotAuthenticate
     * @throws CouldNotCompleteRequest
     * @throws CouldNotReadCodeList when KBO does not know the list (10003)
     * @throws RequestLimitReached
     * @throws UnknownException also when KBO lists a code list this SDK does not know yet
     */
    public function metaCodeList(): MetaCodeListReply
    {
        $unwrapped = $this->sendLookup(new ReadCodeListRequest(CodeListId::MetaCodeList), CouldNotReadCodeList::class);

        return $this->reply(fn () => new MetaCodeListReply($unwrapped->context, $this->codeListMapper->toMetaCodeList($unwrapped->body)));
    }
}
