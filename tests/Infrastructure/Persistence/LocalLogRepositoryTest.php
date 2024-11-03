<?php

namespace Infrastructure\Persistence;

use PHPUnit\Framework\TestCase;
use LyraRingNet\OmsApiClient\Infrastructure\Persistence\LocalLogRepository;
use LyraRingNet\OmsApiClient\Domain\Models\ApiRequest;
use LyraRingNet\OmsApiClient\Domain\Models\ApiResponse;

class LocalLogRepositoryTest extends TestCase
{
    private $localLogRepository;
    private $storagePath = __DIR__ . '/tmp_logs';

    protected function setUp(): void
    {
        $this->localLogRepository = new LocalLogRepository($this->storagePath, '12345');
    }

    protected function tearDown(): void
    {
        $this->deleteDirectoryRecursively($this->storagePath);
    }

    private function deleteDirectoryRecursively($directory): void
    {
        // 獲取目錄內所有文件及子目錄
        $files = glob("{$directory}/*");

        foreach ($files as $file) {
            if (is_dir($file)) {
                // 如果是子目錄，遞迴刪除
                $this->deleteDirectoryRecursively($file);
            } else {
                // 如果是文件，直接刪除
                unlink($file);
            }
        }
        // 刪除空目錄
        rmdir($directory);
    }


    public function testSaveRequestToLocalFile()
    {
        $apiRequest = new ApiRequest([
            'url' => 'https://api.example.com/users',
            'method' => 'GET',
            'headers' => null,
            'body' => null,
            'timestamp' => null
        ]);
        $this->localLogRepository->saveRequest($apiRequest);

        // 使用更寬泛的 glob 路徑
        $files = glob("{$this->storagePath}/12345/api/*/users/*/request.json");
        $this->assertNotEmpty($files, "Request log file should be created.");
        $this->assertJson(file_get_contents($files[0]));
    }

    public function testSaveResponseToLocalFile()
    {
        $apiResponse = new ApiResponse([
            'url' => 'https://api.example.com/users',
            'status' => 200,
            'headers' => null,
            'body' => ['success' => true],
            'timestamp' => null
        ]);
        $this->localLogRepository->saveResponse($apiResponse);

        // 使用更寬泛的 glob 路徑
        $files = glob("{$this->storagePath}/12345/api/*/users/*/response.json");
        $this->assertNotEmpty($files, "Response log file should be created.");
        $this->assertJson(file_get_contents($files[0]));
    }

}
