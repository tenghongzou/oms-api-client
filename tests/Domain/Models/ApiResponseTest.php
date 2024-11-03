<?php

namespace Domain\Models;

use PHPUnit\Framework\TestCase;
use LyraRingNet\OmsApiClient\Domain\Models\ApiResponse;

class ApiResponseTest extends TestCase
{
    public function testApiResponseModelHoldsCorrectData()
    {
        $responseData = [
            'status' => 200,
            'url' => 'https://api.example.com/users',
            'headers' => ['Content-Type' => 'application/json'],
            'body' => ['success' => true],
            'timestamp' => date("Y-m-d H:i:s"),
        ];

        $response = new ApiResponse($responseData);

        $this->assertEquals('https://api.example.com/users', $response->url);
        $this->assertEquals(200, $response->status);
        $this->assertEquals(['Content-Type' => 'application/json'], $response->headers);
        $this->assertEquals(['success' => true], $response->body);
    }
}
