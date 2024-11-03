<?php

namespace LyraRingNet\OmsApiClient\Domain\Exceptions;

use Exception;

class ApiRequestException extends Exception
{
    protected $response;

    public function __construct($message = "API Request failed", $code = 0, Exception $previous = null, $response = null)
    {
        parent::__construct($message, $code, $previous);
        $this->response = $response;
    }

    /**
     * 獲取 API 回應內容（如果有）
     */
    public function getResponse()
    {
        return $this->response;
    }
}
