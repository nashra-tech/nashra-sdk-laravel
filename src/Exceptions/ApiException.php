<?php

namespace Nashra\Sdk\Exceptions;

class ApiException extends NashraException
{
    public function __construct(string $message = 'An unexpected API error occurred.', int $statusCode = 500, ?array $response = null)
    {
        parent::__construct($message, $statusCode, 'SERVER_ERROR', $response);
    }
}
