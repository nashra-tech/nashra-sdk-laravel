<?php

namespace Nashra\Sdk\Exceptions;

class AuthorizationException extends NashraException
{
    public function __construct(string $message = 'No newsletter configured for this account.', ?array $response = null)
    {
        parent::__construct($message, 403, 'MISSING_NEWSLETTER', $response);
    }
}
