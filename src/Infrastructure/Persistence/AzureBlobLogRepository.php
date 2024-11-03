<?php

namespace LyraRingNet\OmsApiClient\Infrastructure\Persistence;

use LyraRingNet\OmsApiClient\Domain\Exceptions\InvalidConfigurationException;
use LyraRingNet\OmsApiClient\Domain\Exceptions\JsonEncodingException;
use LyraRingNet\OmsApiClient\Domain\Models\ApiRequest;
use LyraRingNet\OmsApiClient\Domain\Models\ApiResponse;
use LyraRingNet\OmsApiClient\Domain\Repositories\LogRepositoryInterface;
use LyraRingNet\OmsApiClient\Infrastructure\Factories\CloudStorageFactory;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;

class AzureBlobLogRepository extends BaseLogRepository implements LogRepositoryInterface
{
    protected string $containerName;

    /**
     * @param string $merchantId
     * @param array $config
     * @param BlobRestProxy|null $client
     * @throws InvalidConfigurationException
     */
    public function __construct(
        string         $merchantId,
        array          $config = [],
        ?BlobRestProxy $client = null
    )
    {
        $blobClient = $client ?? CloudStorageFactory::createAzureBlobClient($config);
        $this->containerName = $config['containerName'];

        parent::__construct($merchantId, $blobClient);
    }

    /**
     * @param ApiRequest $request
     * @return void
     * @throws JsonEncodingException
     */
    public function saveRequest(ApiRequest $request): void
    {
        $path = $this->getFilePath($request->url, 'request');
        $this->client->createBlockBlob($this->containerName, $path, $this->toJson($request));
    }

    /**
     * @param ApiResponse $response
     * @return void
     * @throws JsonEncodingException
     */
    public function saveResponse(ApiResponse $response): void
    {
        $path = $this->getFilePath($response->url, 'response');
        $this->client->createBlockBlob($this->containerName, $path, $this->toJson($response));
    }
}
