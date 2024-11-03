<?php

namespace Application\Services;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use PHPUnit\Framework\TestCase;
use LyraRingNet\OmsApiClient\Application\Services\ApiClientService;
use LyraRingNet\OmsApiClient\Application\UseCases\LogApiUseCase;
use Psr\Http\Message\RequestInterface;

class ApiClientServiceTest extends TestCase
{
    private $apiClientService;
    private $logUseCaseMock;

    protected function setUp(): void
    {
        // Mock the LogApiUseCase
        $this->logUseCaseMock = $this->createMock(LogApiUseCase::class);
    }

    public function testRequestSuccessLogsResponse()
    {
        // Set up Guzzle MockHandler with a successful response
        $mock = new MockHandler([
            new Response(200, [], json_encode(['success' => true]))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $this->apiClientService = new ApiClientService($this->logUseCaseMock, 'https://api.example.com', [
            'Authorization' => 'Bearer TEST_TOKEN'
        ], $client);

        $this->logUseCaseMock->expects($this->once())
            ->method('logApiRequestAndResponse');

        $result = $this->apiClientService->request('GET', '/users', ['query' => ['page' => 1]]);
        $this->assertEquals(['success' => true], $result);
    }

    public function testRequestFailureLogsError()
    {
        // Set up Guzzle MockHandler with a failed request
        $mock = new MockHandler([
            new RequestException("Error Communicating with Server", $this->createMock(RequestInterface::class))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $this->apiClientService = new ApiClientService($this->logUseCaseMock, 'https://api.example.com', [
            'Authorization' => 'Bearer TEST_TOKEN'
        ], $client);

        $this->logUseCaseMock->expects($this->once())
            ->method('logApiRequestAndResponse');

        $this->expectException(\LyraRingNet\OmsApiClient\Domain\Exceptions\ApiRequestException::class);
        $this->apiClientService->request('GET', '/users');
    }
}
