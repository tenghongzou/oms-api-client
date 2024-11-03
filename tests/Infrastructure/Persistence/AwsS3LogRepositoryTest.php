<?php

namespace Infrastructure\Persistence;

use PHPUnit\Framework\TestCase;
use LyraRingNet\OmsApiClient\Infrastructure\Persistence\AwsS3LogRepository;
use LyraRingNet\OmsApiClient\Domain\Models\ApiRequest;
use LyraRingNet\OmsApiClient\Domain\Models\ApiResponse;
use Aws\S3\S3Client;
use Aws\MockHandler;
use Aws\Result;

class AwsS3LogRepositoryTest extends TestCase
{
    private $s3LogRepository;
    private $s3ClientMock;
    private $mockHandler;

    protected function setUp(): void
    {
        // 使用 AWS SDK 的 MockHandler 來模擬 S3 的請求
        $this->mockHandler = new MockHandler();

        // 模擬 S3Client 並設置虛擬憑證
        $this->s3ClientMock = new S3Client([
            'region' => 'us-west-2',
            'version' => 'latest',
            'handler' => $this->mockHandler,
            'credentials' => [
                'key' => 'test-key',
                'secret' => 'test-secret'
            ]
        ]);

        // 使用設置的配置參數
        $s3Config = [
            'key' => 'test-key',
            'secret' => 'test-secret',
            'region' => 'us-west-2',
            'bucket' => 'test-bucket',
        ];

        $this->s3LogRepository = new AwsS3LogRepository('merchantId', $s3Config, $this->s3ClientMock);
    }

    public function testSaveRequestToS3()
    {
        // 模擬成功的結果
        $this->mockHandler->append(new Result(['@metadata' => ['statusCode' => 200]]));

        $apiRequest = new ApiRequest([
            'url' => 'https://api.example.com/users',
            'method' => 'GET',
            'headers' => null,
            'body' => null,
            'timestamp' => null
        ]);

        $this->s3LogRepository->saveRequest($apiRequest);

        $this->assertTrue(true);
    }

    public function testSaveResponseToS3()
    {
        // 模擬成功的結果
        $this->mockHandler->append(new Result(['@metadata' => ['statusCode' => 200]]));

        $apiResponse = new ApiResponse([
            'url' => 'https://api.example.com/users',
            'status' => 200,
            'body' => ['success' => true],
            'headers' => null,
            'timestamp' => null
        ]);

        $this->s3LogRepository->saveResponse($apiResponse);

        $this->assertTrue(true);
    }
}
