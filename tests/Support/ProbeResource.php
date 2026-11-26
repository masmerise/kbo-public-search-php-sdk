<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Support;

use KboPublicSearch\Core\Http\KboResource;
use KboPublicSearch\Core\Http\Request;
use KboPublicSearch\Core\Http\UnwrappedReply;

final readonly class ProbeResource extends KboResource
{
    public function probeLookup(Request $request): UnwrappedReply
    {
        return $this->sendLookup($request, CouldNotProbe::class);
    }

    public function probeList(Request $request): UnwrappedReply
    {
        return $this->sendList($request, CouldNotProbe::class);
    }

    public function probeMapping(Request $request): never
    {
        $this->reply(fn () => $this->sendLookup($request, CouldNotProbe::class)->body->child('Missing'));
    }

    public function probeSearch(Request $request): UnwrappedReply
    {
        return $this->sendSearch($request, CouldNotProbe::class);
    }
}
