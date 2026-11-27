<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Http;

use Closure;
use KboPublicSearch\Authentication\Failure\CouldNotAuthenticate;
use KboPublicSearch\Core\Failure\CouldNotCompleteRequest;
use KboPublicSearch\Core\Failure\KboPublicSearchException;
use KboPublicSearch\Core\Failure\RequestLimitReached;
use KboPublicSearch\Core\Failure\UnknownException;
use KboPublicSearch\Core\Failure\ValidationException;
use KboPublicSearch\Core\Reply\ReplyStatus;
use RuntimeException;
use Throwable;

/** @internal */
abstract readonly class KboResource
{
    public function __construct(protected KboConnector $client) {}

    /**
     * A single lookup: only a success is a result; "no data", canceled, unknown, ... throw $onFailure.
     *
     * @param class-string<ValidationException> $onFailure
     *
     * @throws CouldNotAuthenticate
     * @throws CouldNotCompleteRequest
     * @throws RequestLimitReached
     * @throws UnknownException
     * @throws ValidationException
     */
    protected function sendLookup(Request $request, string $onFailure): UnwrappedReply
    {
        return $this->send($request, $onFailure, static fn (ReplyStatus $status) => $status->isSuccess());
    }

    /**
     * A complete list: "no data" yields an empty result; it is never truncated.
     *
     * @param class-string<ValidationException> $onFailure
     *
     * @throws CouldNotAuthenticate
     * @throws CouldNotCompleteRequest
     * @throws RequestLimitReached
     * @throws UnknownException
     * @throws ValidationException
     */
    protected function sendList(Request $request, string $onFailure): UnwrappedReply
    {
        return $this->send($request, $onFailure, static fn (ReplyStatus $status) => $status->isSuccess() || $status->isNoData());
    }

    /**
     * Builds the reply from a result; anything that goes wrong while mapping it is an UnknownException.
     *
     * @template T
     * @param Closure(): T $build
     *
     * @return T
     *
     * @throws KboPublicSearchException
     */
    protected function reply(Closure $build): mixed
    {
        try {
            return $build();
        } catch (KboPublicSearchException $ex) {
            throw $ex;
        } catch (Throwable $ex) {
            throw UnknownException::sorry($ex);
        }
    }

    /**
     * A search: "no data" yields an empty result and more than 200 matches a truncated one.
     *
     * @param class-string<ValidationException> $onFailure
     *
     * @throws CouldNotAuthenticate
     * @throws CouldNotCompleteRequest
     * @throws RequestLimitReached
     * @throws UnknownException
     * @throws ValidationException
     */
    protected function sendSearch(Request $request, string $onFailure): UnwrappedReply
    {
        return $this->send(
            $request,
            $onFailure,
            static fn (ReplyStatus $status) => $status->isSuccess() || $status->isNoData() || $status->isTruncated(),
        );
    }

    /**
     * @param class-string<ValidationException> $onFailure
     * @param Closure(ReplyStatus): bool $accepts
     *
     * @throws KboPublicSearchException
     */
    private function send(Request $request, string $onFailure, Closure $accepts): UnwrappedReply
    {
        try {
            $response = $this->client->send($request);
        } catch (Throwable $ex) {
            throw UnknownException::sorry($ex);
        }

        try {
            $unwrapped = $response->dto();
        } catch (KboPublicSearchException $ex) {
            throw $ex;
        } catch (Throwable $ex) {
            throw UnknownException::sorry($ex);
        }

        if (! $unwrapped instanceof UnwrappedReply) {
            throw UnknownException::sorry(new RuntimeException('The reply could not be read.'));
        }

        if (! $accepts($unwrapped->context->status)) {
            throw new $onFailure($unwrapped->context);
        }

        return $unwrapped;
    }
}
