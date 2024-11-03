<?php

namespace LyraRingNet\OmsApiClient\Infrastructure\Persistence;

use Aws\S3\S3Client;
use Google\Cloud\Storage\StorageClient;
use LyraRingNet\OmsApiClient\Domain\Exceptions\JsonEncodingException;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use Ramsey\Uuid\Uuid;

abstract class BaseLogRepository
{
    protected string $merchantId;
    protected StorageClient|S3Client|null|BlobRestProxy $client;

    /**
     * Constructor to set the merchant ID and client.
     *
     * @param string $merchantId The unique identifier for the merchant.
     * @param S3Client|BlobRestProxy|StorageClient|null $client
     */
    public function __construct(
        string                               $merchantId,
        S3Client|BlobRestProxy|StorageClient $client = null
    )
    {
        $this->merchantId = $merchantId;
        $this->client = $client;
    }

    /**
     * Generates the file path based on the URL, type, GUID, and timestamp.
     *
     * @param string $url The target URL of the API endpoint.
     * @param string $type The type of log (e.g., 'request' or 'response').
     * @return string The generated file path.
     */
    protected function getFilePath(string $url, string $type): string
    {
        $guid = Uuid::uuid4()->toString();
        $timestamp = date('Ymd_His');

        // 解析 URL 獲取 domain 和 endpoint
        $parsedUrl = parse_url($url);
        $domain = $parsedUrl['host'] ?? 'unknown_domain';
        $endpoint = trim($parsedUrl['path'] ?? 'unknown_endpoint', '/');

        // 組合檔案路徑
        return "{$this->merchantId}/api/{$domain}/{$endpoint}/{$timestamp}_{$guid}/{$type}.json";
    }

    /**
     * Converts data to a JSON string with error handling.
     *
     * @param mixed $data The data to be encoded as JSON.
     * @return string The JSON-encoded string.
     * @throws JsonEncodingException if JSON encoding fails.
     */
    protected function toJson(mixed $data): string
    {
        // Encode the data to JSON format with pretty print
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        // Throw custom exception if JSON encoding fails
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonEncodingException('Failed to encode data to JSON: ' . json_last_error_msg());
        }

        return $json;
    }
}
