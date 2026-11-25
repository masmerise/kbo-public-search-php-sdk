<?php declare(strict_types=1);

namespace KboPublicSearch\CodeList;

use KboPublicSearch\Authentication\Failure\CouldNotAuthenticate;
use KboPublicSearch\CodeList\Read\Failure\CouldNotReadCodeList;
use KboPublicSearch\CodeList\Read\Request\ReadCodeListRequest;
use KboPublicSearch\CodeList\Reply\CodeListReply;
use KboPublicSearch\CodeList\Type\CodeListId;
use KboPublicSearch\CodeList\Type\Mapping\MapsCodeLists;
use KboPublicSearch\Core\Failure\CouldNotCompleteRequest;
use KboPublicSearch\Core\Failure\RequestLimitReached;
use KboPublicSearch\Core\Failure\UnknownException;
use KboPublicSearch\Core\Http\KboResource;

/** @internal */
final readonly class CodeListResource extends KboResource
{
    use MapsCodeLists;

    /**
     * Read a code list (SOAP operation ReadCodeList). Free: it does not count towards the daily quota.
     *
     * @param CodeListId $codeListId The code list.
     *
     * @throws CouldNotAuthenticate
     * @throws CouldNotCompleteRequest
     * @throws CouldNotReadCodeList when KBO does not know the list (10003)
     * @throws RequestLimitReached
     * @throws UnknownException
     */
    public function read(CodeListId $codeListId): CodeListReply
    {
        $unwrapped = $this->lookup(new ReadCodeListRequest($codeListId), CouldNotReadCodeList::class);

        return $this->reply(fn () => new CodeListReply($unwrapped->context, $this->toCodeList($unwrapped->body)));
    }
}
