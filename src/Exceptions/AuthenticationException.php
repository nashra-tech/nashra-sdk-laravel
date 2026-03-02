<?php

namespace Nashra\Sdk\Exceptions;

class AuthenticationException extends NashraException
{
    public function __construct(string $message = 'Unauthenticated. Use a valid API token.', ?array $response = null)
    {
        parent::__construct($message, 401, 'INVALID_TOKEN', $response);
    }
}
