<?php

namespace Nashra\Sdk;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Nashra\Sdk\Exceptions\ApiException;
use Nashra\Sdk\Exceptions\AuthenticationException;
use Nashra\Sdk\Exceptions\AuthorizationException;
use Nashra\Sdk\Exceptions\NotFoundException;
use Nashra\Sdk\Exceptions\RateLimitException;
use Nashra\Sdk\Exceptions\ValidationException;

class NashraClient
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $baseUrl,
        private readonly int $timeout,
        private readonly int $maxRetries,
    ) {}

    public function get(string $path, array $query = []): array
    {
        $response = $this->request()->get($this->url($path), $query);

        return $this->handleResponse($response);
    }

    public function post(string $path, array $data = []): array
    {
        $response = $this->request()->post($this->url($path), $data);

        return $this->handleResponse($response);
    }

    public function patch(string $path, array $data = []): array
    {
        $response = $this->request()->patch($this->url($path), $data);

        return $this->handleResponse($response);
    }

    public function delete(string $path): array
    {
        $response = $this->request()->delete($this->url($path));

        return $this->handleResponse($response);
    }

    private function request(): PendingRequest
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer '.$this->apiKey,
            'Accept' => 'application/json',
        ])
            ->timeout($this->timeout)
            ->retry(
                $this->maxRetries,
                function (int $attempt) {
                    return $attempt * 1000;
                },
                function (\Exception $exception) {
                    if (! $exception instanceof \Illuminate\Http\Client\RequestException) {
                        return false;
                    }

                    $status = $exception->response->status();

                    return $status === 429 || $status >= 500;
                },
                throw: false,
            );
    }

    private function url(string $path): string
    {
        return rtrim($this->baseUrl, '/').'/'.ltrim($path, '/');
    }

    private function handleResponse(Response $response): array
    {
        if ($response->successful()) {
            return $response->json() ?? [];
        }

        $body = $response->json() ?? [];
        $error = $body['error'] ?? [];
        $message = $error['message'] ?? 'Unknown error';
        $status = $response->status();

        match ($status) {
            401 => throw new AuthenticationException($message, $body),
            403 => throw new AuthorizationException($message, $body),
            404 => throw new NotFoundException($message, $body),
            422 => throw new ValidationException($message, $error['details'] ?? [], $body),
            429 => throw new RateLimitException((int) $response->header('Retry-After', 60), $body),
            default => throw new ApiException($message, $status, $body),
        };
    }
}
