<?php

namespace Nashra\Sdk\Exceptions;

use RuntimeException;

class NashraException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly int $statusCode,
        public readonly string $errorCode = '',
        public readonly ?array $response = null,
    ) {
        parent::__construct($message, $statusCode);
    }
}
