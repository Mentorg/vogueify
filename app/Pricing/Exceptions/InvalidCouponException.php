<?php

namespace App\Pricing\Exceptions;

use Exception;

class InvalidCouponException extends Exception
{
    public string $errorType;

    public function __construct(string $errorType, string $message)
    {
        parent::__construct($message);
        $this->errorType = $errorType;
    }
}
