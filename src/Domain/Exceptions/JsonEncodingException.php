<?php

namespace LyraRingNet\OmsApiClient\Domain\Exceptions;

use Exception;

class JsonEncodingException extends Exception
{
    /**
     * Custom exception for JSON encoding errors.
     *
     * @param string $message Custom error message.
     * @param int $code Error code (optional).
     * @param Exception|null $previous Previous exception for nested exceptions (optional).
     */
    public function __construct(string $message = "Failed to encode data to JSON", int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
