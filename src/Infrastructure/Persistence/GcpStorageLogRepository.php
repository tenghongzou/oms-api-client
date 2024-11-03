<?php

namespace LyraRingNet\OmsApiClient\Infrastructure\Persistence;

use LyraRingNet\OmsApiClient\Domain\Exceptions\InvalidConfigurationException;
use LyraRingNet\OmsApiClient\Domain\Exceptions\JsonEncodingException;
use LyraRingNet\OmsApiClient\Domain\Models\ApiRequest;
use LyraRingNet\OmsApiClient\Domain\Models\ApiResponse;
use LyraRingNet\OmsApiClient\Domain\Repositories\LogRepositoryInterface;
use Google\Cloud\Storage\StorageClient;
use LyraRingNet\OmsApiClient\Infrastructure\Factories\CloudStorageFactory;

class GcpStorageLogRepository extends BaseLogRepository implements LogRepositoryInterface
{
    protected StorageClient $storage;
    protected string $bucketName;

    /**
     * @param array $config
     * @param string $merchantId
     * @param StorageClient|null $client
     * @throws InvalidConfigurationException
     */
    public function __construct(
        string $merchantId,
        array $config = [],
        ?StorageClient $client = null
    )
    {
        $storageClient = $client ?? CloudStorageFactory::createGcpClient($config);
        $this->bucketName = $config['bucketName'];

        parent::__construct($merchantId, $storageClient);
    }

    /**
     * @param ApiRequest $request
     * @return void
     * @throws JsonEncodingException
     */
    public function saveRequest(ApiRequest $request): void
    {
        $path = $this->getFilePath($request->url, 'request');
        $bucket = $this->client->bucket($this->bucketName);

        $bucket->upload($this->toJson($request), [
            'name' => $path,
            'metadata' => ['contentType' => 'application/json']
        ]);
    }

    /**
     * @param ApiResponse $response
     * @return void
     * @throws JsonEncodingException
     */
    public function saveResponse(ApiResponse $response): void
    {
        $path = $this->getFilePath($response->url, 'response');
        $bucket = $this->client->bucket($this->bucketName);

        $bucket->upload($this->toJson($response), [
            'name' => $path,
            'metadata' => ['contentType' => 'application/json']
        ]);
    }
}
