<?php

namespace Domain\Models;

use PHPUnit\Framework\TestCase;
use LyraRingNet\OmsApiClient\Domain\Models\ApiRequest;

class ApiRequestTest extends TestCase
{
    public function testApiRequestModelHoldsCorrectData()
    {
        $requestData = [
            'method' => 'GET',
            'url' => 'https://api.example.com/users',
            'headers' => ['Authorization' => 'Bearer TOKEN'],
            'body' => ['query' => ['page' => 1]],
            'timestamp' => date("Y-m-d H:i:s"),
        ];

        $request = new ApiRequest($requestData);

        $this->assertEquals('https://api.example.com/users', $request->url);
        $this->assertEquals('GET', $request->method);
        $this->assertEquals(['Authorization' => 'Bearer TOKEN'], $request->headers);
        $this->assertEquals(['query' => ['page' => 1]], $request->body);
    }
}
