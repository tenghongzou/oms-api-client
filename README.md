
# OmsApiClient

`OmsApiClient` 是一個用於記錄 API 請求和回應的 PHP package，支持將日誌存儲在本地檔案系統、AWS S3、Google Cloud Storage (GCP) 和 Azure Blob Storage 中。

## 目錄

- [安裝](#安裝)
- [依賴](#依賴)
- [配置](#配置)
- [用法](#用法)
  - [本地日誌存儲](#本地日誌存儲)
  - [AWS S3 日誌存儲](#aws-s3-日誌存儲)
  - [Google Cloud Storage 日誌存儲](#google-cloud-storage-日誌存儲)
  - [Azure Blob Storage 日誌存儲](#azure-blob-storage-日誌存儲)
- [例外處理](#例外處理)
- [開發人員](#開發人員)
- [License](#license)

## 安裝

1. 安裝 package：

   ```bash
   composer require lyra-ring-net/oms-api-client
   ```

## 依賴

`OmsApiClient` 依賴以下第三方套件：

- **Guzzle**：用於發送 HTTP 請求。
- **AWS SDK**：支持將日誌上傳到 AWS S3。
- **Google Cloud Storage SDK**：支持將日誌上傳到 Google Cloud Storage。
- **Azure Blob Storage SDK**：支持將日誌上傳到 Azure Blob Storage。
- **ramsey/uuid**：生成唯一日誌文件名。

## 配置

### 環境變量配置

建議使用 `.env` 文件來存放敏感配置信息。根據所選儲存平台添加相關配置。

#### AWS S3 配置

```plaintext
AWS_ACCESS_KEY_ID=your-aws-key
AWS_SECRET_ACCESS_KEY=your-aws-secret
AWS_REGION=us-west-2
AWS_BUCKET_NAME=your-bucket-name
```

#### Google Cloud Storage 配置

```plaintext
GCP_KEY_FILE_PATH=/path/to/your-service-account-file.json
GCP_BUCKET_NAME=your-gcp-bucket-name
```

#### Azure Blob Storage 配置

```plaintext
AZURE_ACCOUNT_NAME=your-account-name
AZURE_ACCOUNT_KEY=your-account-key
AZURE_CONTAINER_NAME=your-container-name
```

#### 本地存儲配置

```plaintext
LOCAL_STORAGE_PATH=./storage/logs
```

在代碼中載入 `.env` 配置：

```php
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
```

## 用法

使用 `OmsApiClient` 進行 API 日誌記錄時，需要配置日誌儲存庫。以下是每種日誌存儲方式的使用示例。

### 本地日誌存儲

以下是在本地檔案系統中存儲日誌的示例。

```php
use LyraRingNet\OmsApiClient\Application\Services\ApiClientService;
use LyraRingNet\OmsApiClient\Application\UseCases\LogApiUseCase;
use LyraRingNet\OmsApiClient\Infrastructure\Persistence\LocalLogRepository;

$storagePath = getenv('LOCAL_STORAGE_PATH');
$merchantId = '12345';

$localLogRepository = new LocalLogRepository($storagePath, $merchantId);
$logUseCase = new LogApiUseCase($localLogRepository);
$apiClientService = new ApiClientService($logUseCase, 'https://api.example.com', ['Authorization' => 'Bearer YOUR_API_TOKEN']);

$response = $apiClientService->request('GET', '/users', ['query' => ['page' => 1]]);
print_r($response);
```

### AWS S3 日誌存儲

使用 AWS S3 作為日誌儲存的示例：

```php
use LyraRingNet\OmsApiClient\Application\Services\ApiClientService;
use LyraRingNet\OmsApiClient\Application\UseCases\LogApiUseCase;
use LyraRingNet\OmsApiClient\Infrastructure\Persistence\AwsS3LogRepository;
use LyraRingNet\OmsApiClient\Infrastructure\Factories\CloudStorageFactory;

$s3Config = [
    'key' => getenv('AWS_ACCESS_KEY_ID'),
    'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
    'region' => getenv('AWS_REGION'),
    'bucket' => getenv('AWS_BUCKET_NAME'),
];

$merchantId = '12345';

// 選擇配置方式 1：傳入配置和 merchantId
$awsS3LogRepository = new AwsS3LogRepository($merchantId, $s3Config);

// 或 選擇配置方式 2：直接傳入 AWS S3 客戶端
$s3Client = CloudStorageFactory::createS3Client($s3Config);
$awsS3LogRepository = new AwsS3LogRepository($merchantId, [], $s3Client);

$logUseCase = new LogApiUseCase($awsS3LogRepository);
$apiClientService = new ApiClientService($logUseCase, 'https://api.example.com', ['Authorization' => 'Bearer YOUR_API_TOKEN']);

// 發送 API 請求並打印回應
$response = $apiClientService->request('GET', '/users', ['query' => ['page' => 1]]);
print_r($response);

```

### Google Cloud Storage 日誌存儲

使用 Google Cloud Storage 作為日誌儲存的示例：

```php
use LyraRingNet\OmsApiClient\Application\Services\ApiClientService;
use LyraRingNet\OmsApiClient\Application\UseCases\LogApiUseCase;
use LyraRingNet\OmsApiClient\Infrastructure\Persistence\GcpStorageLogRepository;
use LyraRingNet\OmsApiClient\Infrastructure\Factories\CloudStorageFactory;

$gcpConfig = [
    'keyFilePath' => getenv('GCP_KEY_FILE_PATH'),
    'bucketName' => getenv('GCP_BUCKET_NAME'),
];

$merchantId = '12345';
// 選擇配置方式 1：傳入配置和 merchantId
$gcpStorageLogRepository = new GcpStorageLogRepository($merchantId, $gcpConfig);

// 或 選擇配置方式 2：直接傳入 Google Cloud Storage 客戶端
$gcpStorageLogRepository = new GcpStorageLogRepository($merchantId, [], CloudStorageFactory::createGcpClient($gcpConfig));

$logUseCase = new LogApiUseCase($gcpStorageLogRepository);
$apiClientService = new ApiClientService($logUseCase, 'https://api.example.com', ['Authorization' => 'Bearer YOUR_API_TOKEN']);

$response = $apiClientService->request('GET', '/users', ['query' => ['page' => 1]]);
print_r($response);
```

### Azure Blob Storage 日誌存儲

使用 Azure Blob Storage 作為日誌儲存的示例：

```php
use LyraRingNet\OmsApiClient\Application\Services\ApiClientService;
use LyraRingNet\OmsApiClient\Application\UseCases\LogApiUseCase;
use LyraRingNet\OmsApiClient\Infrastructure\Persistence\AzureBlobLogRepository;
use LyraRingNet\OmsApiClient\Infrastructure\Factories\CloudStorageFactory;

$azureConfig = [
    'accountName' => getenv('AZURE_ACCOUNT_NAME'),
    'accountKey' => getenv('AZURE_ACCOUNT_KEY'),
    'containerName' => getenv('AZURE_CONTAINER_NAME'),
];

$merchantId = '12345';
// 選擇配置方式 1：傳入配置和 merchantId
$azureBlobLogRepository = new AzureBlobLogRepository($merchantId, $azureConfig);

// 或 選擇配置方式 2：直接傳入 Azure Blob Storage 客戶端
$azureBlobLogRepository = new AzureBlobLogRepository($merchantId, [], CloudStorageFactory::createAzureBlobClient($azureConfig));

$logUseCase = new LogApiUseCase($azureBlobLogRepository);
$apiClientService = new ApiClientService($logUseCase, 'https://api.example.com', ['Authorization' => 'Bearer YOUR_API_TOKEN']);

$response = $apiClientService->request('GET', '/users', ['query' => ['page' => 1]]);
print_r($response);
```

## 例外處理

`OmsApiClient` 提供以下自定義例外來幫助捕獲和處理錯誤：

- **ApiRequestException**：此類別用於捕獲 API 請求失敗時的錯誤，並包含 API 回應的內容。
- **JsonEncodingException**：當日誌資料無法成功編碼為 JSON 時拋出。
- **InvalidConfigurationException**：當配置不完整或無效時拋出。

### 捕獲例外示例

```php
try {
    $response = $apiClientService->request('GET', '/users', ['query' => ['page' => 1]]);
    print_r($response);

} catch (ApiRequestException $e) {
    echo "API 請求錯誤: " . $e->getMessage();
    if ($e->getResponse()) {
        echo "\n回應內容: " . $e->getResponse();
    }
} catch (JsonEncodingException $e) {
    echo "JSON 編碼錯誤: " . $e->getMessage();
} catch (InvalidConfigurationException $e) {
    echo "配置錯誤: " . $e->getMessage();
}
```

## 開發人員

- **作者**：LyraRingNet
- **聯絡方式**：
## License

`OmsApiClient` 是開源軟體，遵循 MIT 授權條款。詳情請參閱 [LICENSE](./LICENSE) 文件。
