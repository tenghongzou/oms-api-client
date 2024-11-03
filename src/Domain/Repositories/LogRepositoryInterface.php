<?php

namespace LyraRingNet\OmsApiClient\Domain\Repositories;

use LyraRingNet\OmsApiClient\Domain\Models\ApiRequest;
use LyraRingNet\OmsApiClient\Domain\Models\ApiResponse;

interface LogRepositoryInterface
{
    public function saveRequest(ApiRequest $request): void;

    public function saveResponse(ApiResponse $response): void;
}
