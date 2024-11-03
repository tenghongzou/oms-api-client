<?php

namespace LyraRingNet\OmsApiClient\Application\UseCases;

use LyraRingNet\OmsApiClient\Domain\Models\ApiRequest;
use LyraRingNet\OmsApiClient\Domain\Models\ApiResponse;
use LyraRingNet\OmsApiClient\Domain\Repositories\LogRepositoryInterface;

class LogApiUseCase
{
    protected LogRepositoryInterface $logRepository;

    public function __construct(LogRepositoryInterface $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    public function logApiRequestAndResponse(array $requestData, array $responseData): void
    {
        $request = new ApiRequest($requestData);
        $response = new ApiResponse($responseData);

        $this->logRepository->saveRequest($request);
        $this->logRepository->saveResponse($response);
    }
}
