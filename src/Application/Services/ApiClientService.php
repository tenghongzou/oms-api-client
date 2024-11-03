<?php

namespace LyraRingNet\OmsApiClient\Application\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use LyraRingNet\OmsApiClient\Domain\Exceptions\ApiRequestException;
use LyraRingNet\OmsApiClient\Application\UseCases\LogApiUseCase;

class ApiClientService
{
    protected Client $client;
    protected LogApiUseCase $logUseCase;

    public function __construct(LogApiUseCase $logUseCase, string $baseUri, array $headers = [], Client $client = null)
    {
        $this->logUseCase = $logUseCase;
        $this->client = $client ?? new Client([
            'base_uri' => $baseUri,
            'headers' => $headers
        ]);
    }

    public function request($method, $uri, $options = [])
    {
        $requestLog = [
            'method' => $method,
            'url' => $this->client->getConfig('base_uri') . $uri,
            'headers' => $options['headers'] ?? $this->client->getConfig('headers'),
            'body' => $options['json'] ?? $options['form_params'] ?? null,
            'timestamp' => $this->getTimeStamp()
        ];

        $responseLog = [];
        try {
            $response = $this->client->request($method, $uri, $options);
            $responseLog = [
                'status' => $response->getStatusCode(),
                'url' => $requestLog['url'],
                'headers' => $response->getHeaders(),
                'body' => json_decode($response->getBody(), true),
                'timestamp' => $this->getTimeStamp()
            ];
        } catch (RequestException $e) {
            // 捕獲 Guzzle 的 RequestException 並轉換為自定義的 ApiRequestException
            $responseLog = [
                'error' => true,
                'message' => $e->getMessage(),
                'timestamp' => $this->getTimeStamp()
            ];

            $this->logUseCase->logApiRequestAndResponse($requestLog, $responseLog);

            throw new ApiRequestException(
                'API 請求失敗: ' . $e->getMessage(),
                $e->getCode(),
                $e,
                $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null
            );
        }

        // 成功情況下的日誌
        $this->logUseCase->logApiRequestAndResponse($requestLog, $responseLog);

        return $responseLog['body'] ?? $responseLog;
    }

    private function getTimeStamp(): string
    {
        return date('Y-m-d H:i:s');
    }
}
