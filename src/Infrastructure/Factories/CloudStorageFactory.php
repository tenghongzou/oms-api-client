<?php

namespace LyraRingNet\OmsApiClient\Infrastructure\Factories;

use Aws\S3\S3Client;
use Google\Cloud\Storage\StorageClient;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use LyraRingNet\OmsApiClient\Domain\Exceptions\InvalidConfigurationException;

class CloudStorageFactory
{
    /**
     * @param array $config
     * @return S3Client
     * @throws InvalidConfigurationException
     */
    public static function createS3Client(array $config): S3Client
    {
        if (empty($config['key']) || empty($config['secret']) || empty($config['region'])) {
            throw new InvalidConfigurationException("AWS S3 configuration is incomplete.");
        }

        return new S3Client([
            'version' => 'latest',
            'region' => $config['region'],
            'credentials' => [
                'key' => $config['key'],
                'secret' => $config['secret'],
            ],
        ]);
    }

    /**
     * @param array $config
     * @return StorageClient
     * @throws InvalidConfigurationException
     */
    public static function createGcpClient(array $config): StorageClient
    {
        if (empty($config['keyFilePath'])) {
            throw new InvalidConfigurationException("GCP configuration is incomplete.");
        }

        return new StorageClient([
            'keyFilePath' => $config['keyFilePath'],
        ]);
    }

    /**
     * @param array $config
     * @return BlobRestProxy
     * @throws InvalidConfigurationException
     */
    public static function createAzureBlobClient(array $config): BlobRestProxy
    {
        if (empty($config['accountName']) || empty($config['accountKey'])) {
            throw new InvalidConfigurationException("Azure Blob Storage configuration is incomplete.");
        }

        $connectionString = "DefaultEndpointsProtocol=https;AccountName={$config['accountName']};AccountKey={$config['accountKey']}";
        return BlobRestProxy::createBlobService($connectionString);
    }
}
