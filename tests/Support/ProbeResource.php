<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Support;

use KboPublicSearch\Core\Http\KboResource;
use KboPublicSearch\Core\Http\Request;
use KboPublicSearch\Core\Http\Result;

final readonly class ProbeResource extends KboResource
{
    public function probeLookup(Request $request): Result
    {
        return $this->lookup($request, CouldNotProbe::class);
    }

    public function probeSearch(Request $request): Result
    {
        return $this->search($request, CouldNotProbe::class);
    }
}
