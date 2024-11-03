<?php

namespace Application\UseCases;

use PHPUnit\Framework\TestCase;
use LyraRingNet\OmsApiClient\Application\UseCases\LogApiUseCase;
use LyraRingNet\OmsApiClient\Domain\Repositories\LogRepositoryInterface;
use LyraRingNet\OmsApiClient\Domain\Models\ApiRequest;
use LyraRingNet\OmsApiClient\Domain\Models\ApiResponse;

class LogApiUseCaseTest extends TestCase
{
    private $logApiUseCase;
    private $logRepositoryMock;

    protected function setUp(): void
    {
        $this->logRepositoryMock = $this->createMock(LogRepositoryInterface::class);
        $this->logApiUseCase = new LogApiUseCase($this->logRepositoryMock);
    }

    public function testLogApiRequestAndResponse()
    {
        $requestLog = ['url' => 'https://api.example.com/users', 'method' => 'GET'];
        $responseLog = ['status' => 200, 'body' => ['success' => true]];

        $this->logRepositoryMock->expects($this->once())
                                ->method('saveRequest')
                                ->with($this->isInstanceOf(ApiRequest::class));

        $this->logRepositoryMock->expects($this->once())
                                ->method('saveResponse')
                                ->with($this->isInstanceOf(ApiResponse::class));

        $this->logApiUseCase->logApiRequestAndResponse($requestLog, $responseLog);
    }
}
