<?php

namespace OdooJson2\Exceptions;

use Psr\Http\Message\ResponseInterface;

class AuthenticationException extends OdooException
{
    public function __construct(?ResponseInterface $response = null, string $message = "Authentication failed!", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($response, $message, $code, $previous);
    }
}

