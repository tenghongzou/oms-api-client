<?php

namespace LyraRingNet\OmsApiClient\Interfaces\Controllers;

use LyraRingNet\OmsApiClient\Application\Services\ApiClientService;

class ApiLoggerController
{
    protected ApiClientService $apiClient;

    public function __construct(ApiClientService $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function logApiRequest($method, $uri, $options = [])
    {
        return $this->apiClient->request($method, $uri, $options);
    }
}
