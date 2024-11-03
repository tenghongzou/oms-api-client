<?php

namespace LyraRingNet\OmsApiClient\Infrastructure\Persistence;

use LyraRingNet\OmsApiClient\Domain\Exceptions\InvalidConfigurationException;
use LyraRingNet\OmsApiClient\Domain\Exceptions\JsonEncodingException;
use LyraRingNet\OmsApiClient\Domain\Models\ApiRequest;
use LyraRingNet\OmsApiClient\Domain\Models\ApiResponse;
use LyraRingNet\OmsApiClient\Domain\Repositories\LogRepositoryInterface;
use Aws\S3\S3Client;
use LyraRingNet\OmsApiClient\Infrastructure\Factories\CloudStorageFactory;

class AwsS3LogRepository extends BaseLogRepository implements LogRepositoryInterface
{
    protected string $bucket;

    /**
     * @param string $merchantId
     * @param array $config
     * @param S3Client|null $client
     * @throws InvalidConfigurationException
     */
    public function __construct(
        string   $merchantId,
        array    $config = [],
        ?S3Client $client = null
    ) {
        // 初始化 client 和 bucket
        $s3client = $client ?? CloudStorageFactory::createS3Client($config);
        $this->bucket = $config['bucket'];

        // 調用父類的構造函數，並確保傳遞的 client 是最終的 client
        parent::__construct($merchantId, $s3client);
    }

    /**
     * @param ApiRequest $request
     * @return void
     * @throws JsonEncodingException
     */
    public function saveRequest(ApiRequest $request): void
    {
        $path = $this->getFilePath($request->url, 'request');
        $this->client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $path,
            'Body' => $this->toJson($request),
            'ContentType' => 'application/json'
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
        $this->client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $path,
            'Body' => $this->toJson($response),
            'ContentType' => 'application/json'
        ]);
    }
}
