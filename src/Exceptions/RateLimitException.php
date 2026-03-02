<?php

namespace Nashra\Sdk\Exceptions;

class RateLimitException extends NashraException
{
    private int $retryAfterSeconds;

    public function __construct(int $retryAfter = 60, ?array $response = null)
    {
        parent::__construct('Rate limit exceeded. Retry after '.$retryAfter.' seconds.', 429, 'RATE_LIMITED', $response);
        $this->retryAfterSeconds = $retryAfter;
    }

    public function retryAfter(): int
    {
        return $this->retryAfterSeconds;
    }
}
