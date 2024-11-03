<?php

namespace Infrastructure\Persistence;

use PHPUnit\Framework\TestCase;
use LyraRingNet\OmsApiClient\Infrastructure\Persistence\AzureBlobLogRepository;
use LyraRingNet\OmsApiClient\Domain\Models\ApiRequest;
use LyraRingNet\OmsApiClient\Domain\Models\ApiResponse;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;

class AzureBlobLogRepositoryTest extends TestCase
{
    private $azureLogRepository;
    private $blobClientMock;

    protected function setUp(): void
    {
        $azureConfig = [
            'accountName' => 'test-account',
            'accountKey' => base64_encode('test-key'),
            'containerName' => 'test-container'
        ];

        $this->blobClientMock = $this->getMockBuilder(BlobRestProxy::class)
                                     ->disableOriginalConstructor()
                                     ->onlyMethods(['createBlockBlob'])
                                     ->getMock();

        $this->azureLogRepository = new AzureBlobLogRepository('merchantId', $azureConfig, $this->blobClientMock);
    }

    public function testSaveRequestToAzureBlob()
    {
        $apiRequest = new ApiRequest([
            'url' => 'https://api.example.com/users',
            'method' => 'GET',
            'headers' => null,
            'body' => null,
            'timestamp' => null
        ]);

        $this->blobClientMock->expects($this->once())
                             ->method('createBlockBlob')
                             ->with($this->equalTo('test-container'), $this->stringContains('request'))
                             ->willReturn(true);

        $this->azureLogRepository->saveRequest($apiRequest);
    }

    public function testSaveResponseToAzureBlob()
    {
        $apiResponse = new ApiResponse([
            'url' => 'https://api.example.com/users',
            'status' => 200,
            'body' => ['success' => true],
            'headers' => null,
            'timestamp' => null
        ]);

        $this->blobClientMock->expects($this->once())
                             ->method('createBlockBlob')
                             ->with($this->equalTo('test-container'), $this->stringContains('response'))
                             ->willReturn(true);

        $this->azureLogRepository->saveResponse($apiResponse);
    }
}
