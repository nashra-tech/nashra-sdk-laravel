<?php

namespace Nashra\Sdk\Exceptions;

class NotFoundException extends NashraException
{
    public function __construct(string $message = 'Resource not found', ?array $response = null)
    {
        parent::__construct($message, 404, 'NOT_FOUND', $response);
    }
}
