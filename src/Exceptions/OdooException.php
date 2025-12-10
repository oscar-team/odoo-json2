<?php

namespace OdooJson2\Exceptions;

use JetBrains\PhpStorm\Pure;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

class OdooException extends RuntimeException
{
    #[Pure] public function __construct(protected ?ResponseInterface $response, string $message = "", $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

