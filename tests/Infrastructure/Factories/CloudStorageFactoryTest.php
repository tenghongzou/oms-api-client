<?php

namespace Infrastructure\Factories;

use PHPUnit\Framework\TestCase;
use Google\Cloud\Storage\StorageClient;
use LyraRingNet\OmsApiClient\Infrastructure\Factories\CloudStorageFactory;

class CloudStorageFactoryTest extends TestCase
{
    public function testCreateGcpClient()
    {
        $config = [
            'keyFilePath' => sys_get_temp_dir() . '/fake_key.json'
        ];
        file_put_contents($config['keyFilePath'], json_encode([]));

        $client = CloudStorageFactory::createGcpClient($config);

        $this->assertInstanceOf(StorageClient::class, $client);
    }
}

