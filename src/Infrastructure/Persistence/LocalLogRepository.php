<?php

namespace LyraRingNet\OmsApiClient\Infrastructure\Persistence;

use LyraRingNet\OmsApiClient\Domain\Models\ApiRequest;
use LyraRingNet\OmsApiClient\Domain\Models\ApiResponse;
use LyraRingNet\OmsApiClient\Domain\Repositories\LogRepositoryInterface;
use LyraRingNet\OmsApiClient\Domain\Exceptions\JsonEncodingException;

class LocalLogRepository extends BaseLogRepository implements LogRepositoryInterface
{
    protected string $storagePath;

    /**
     * @param string $storagePath
     * @param string $merchantId
     */
    public function __construct(string $storagePath, string $merchantId)
    {
        parent::__construct($merchantId);
        $this->storagePath = rtrim($storagePath, '/') . '/';

        // 確認存儲路徑是否存在，若不存在則創建
        if (!is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0777, true);
        }
    }

    /**
     * @param ApiRequest $request
     * @return void
     * @throws JsonEncodingException
     */
    public function saveRequest(ApiRequest $request): void
    {
        $path = $this->getFilePath($request->url, 'request');
        $this->writeToFile($path, $request);
    }

    /**
     * @param ApiResponse $response
     * @return void
     * @throws JsonEncodingException
     */
    public function saveResponse(ApiResponse $response): void
    {
        $path = $this->getFilePath($response->url, 'response');
        $this->writeToFile($path, $response);
    }

    /**
     * @param $path
     * @param $data
     * @return void
     * @throws JsonEncodingException
     */
    private function writeToFile($path, $data): void
    {
        $fullPath = $this->storagePath . $path;

        // 確認路徑中的目錄是否存在
        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        // 編碼為 JSON 並寫入檔案
        $jsonData = $this->toJson($data);
        file_put_contents($fullPath, $jsonData);
    }
}
