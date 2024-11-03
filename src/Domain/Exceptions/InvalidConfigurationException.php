<?php

namespace LyraRingNet\OmsApiClient\Domain\Exceptions;

use Exception;

class InvalidConfigurationException extends Exception
{
    public function __construct($message = "Invalid configuration", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
