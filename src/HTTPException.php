<?php

namespace Burrow;

use Exception;

class HTTPException extends Exception
{
    public function __construct(string $message, int $code = 0, Exception|null $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
