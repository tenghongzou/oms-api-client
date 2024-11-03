<?php

namespace Infrastructure\Persistence;

use PHPUnit\Framework\TestCase;
use LyraRingNet\OmsApiClient\Infrastructure\Persistence\GcpStorageLogRepository;
use LyraRingNet\OmsApiClient\Domain\Models\ApiRequest;
use LyraRingNet\OmsApiClient\Domain\Models\ApiResponse;
use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Storage\Bucket;
use LyraRingNet\OmsApiClient\Infrastructure\Factories\CloudStorageFactory;

class GcpStorageLogRepositoryTest extends TestCase
{
    private $gcpLogRepository;
    private $storageClientMock;
    private $bucketMock;

    protected function setUp(): void
    {
        // 模擬 GCP 配置
        $gcpConfig = [
            'keyFilePath' => sys_get_temp_dir() . '/fake_key.json',
            'bucketName' => 'test-bucket'
        ];
        file_put_contents($gcpConfig['keyFilePath'], json_encode([]));

        // 建立 Bucket 模擬
        $this->bucketMock = $this->createMock(Bucket::class);

        // 建立 StorageClient 模擬，並設定返回 bucketMock
        $this->storageClientMock = $this->getMockBuilder(StorageClient::class)
                                        ->disableOriginalConstructor()
                                        ->onlyMethods(['bucket'])
                                        ->getMock();
        $this->storageClientMock->method('bucket')->willReturn($this->bucketMock);

        // 初始化 GcpStorageLogRepository，並將 merchantId 和 config 傳入
        $this->gcpLogRepository = new GcpStorageLogRepository('merchantId', $gcpConfig, $this->storageClientMock);
    }

    protected function tearDown(): void
    {
        $gcpConfig = sys_get_temp_dir() . '/fake_key.json';
        if (file_exists($gcpConfig)) {
            unlink($gcpConfig);
        }
    }

    public function testSaveRequestToGcp()
    {
        $apiRequest = new ApiRequest([
            'url' => 'https://api.example.com/users',
            'method' => 'GET',
            'headers' => null,
            'body' => null,
            'timestamp' => null
        ]);

        $this->bucketMock->expects($this->once())
                         ->method('upload')
                         ->with(
                             $this->isType('string'),
                             $this->arrayHasKey('name')
                         )
                         ->willReturn(true); // 模擬 `upload` 方法的返回值，避免實際請求

        $this->gcpLogRepository->saveRequest($apiRequest);
    }

    public function testSaveResponseToGcp()
    {
        $apiResponse = new ApiResponse([
            'url' => 'https://api.example.com/users',
            'status' => 200,
            'body' => ['success' => true],
            'headers' => null,
            'timestamp' => null
        ]);

        $this->bucketMock->expects($this->once())
                         ->method('upload')
                         ->with(
                             $this->isType('string'),
                             $this->arrayHasKey('name')
                         )
                         ->willReturn(true); // 模擬 `upload` 方法的返回值，避免實際請求

        $this->gcpLogRepository->saveResponse($apiResponse);
    }
}
