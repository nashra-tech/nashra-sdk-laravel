<?php

use Illuminate\Support\Facades\Http;
use Nashra\Sdk\Exceptions\ApiException;
use Nashra\Sdk\Exceptions\AuthenticationException;
use Nashra\Sdk\Exceptions\AuthorizationException;
use Nashra\Sdk\Exceptions\NotFoundException;
use Nashra\Sdk\Exceptions\RateLimitException;
use Nashra\Sdk\Exceptions\ValidationException;
use Nashra\Sdk\NashraClient;

beforeEach(function () {
    $this->client = new NashraClient(
        apiKey: 'test-key',
        baseUrl: 'https://app.nashra.ai/api/v1',
        timeout: 30,
        maxRetries: 0,
    );
});

it('sends authorization header on requests', function () {
    Http::fake([
        'app.nashra.ai/*' => Http::response(['data' => [], 'meta' => ['version' => 'v1', 'timestamp' => now()->toISOString()]]),
    ]);

    $this->client->get('/subscribers');

    Http::assertSent(function ($request) {
        return $request->hasHeader('Authorization', 'Bearer test-key')
            && $request->hasHeader('Accept', 'application/json');
    });
});

it('sends GET requests with query parameters', function () {
    Http::fake([
        'app.nashra.ai/*' => Http::response(['data' => [], 'meta' => ['version' => 'v1', 'timestamp' => now()->toISOString()]]),
    ]);

    $this->client->get('/subscribers', ['search' => 'john', 'per_page' => 50]);

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'search=john')
            && str_contains($request->url(), 'per_page=50');
    });
});

it('sends POST requests with body', function () {
    Http::fake([
        'app.nashra.ai/*' => Http::response([
            'data' => ['uuid' => 'abc', 'email' => 'j@e.com'],
            'meta' => ['version' => 'v1', 'timestamp' => now()->toISOString()],
        ], 201),
    ]);

    $result = $this->client->post('/subscribers', ['email' => 'j@e.com']);

    expect($result['data']['email'])->toBe('j@e.com');

    Http::assertSent(function ($request) {
        return $request->method() === 'POST'
            && $request['email'] === 'j@e.com';
    });
});

it('sends PATCH requests', function () {
    Http::fake([
        'app.nashra.ai/*' => Http::response([
            'data' => ['uuid' => 'abc', 'name' => 'Updated'],
            'meta' => ['version' => 'v1', 'timestamp' => now()->toISOString()],
        ]),
    ]);

    $this->client->patch('/tags/abc', ['name' => 'Updated']);

    Http::assertSent(function ($request) {
        return $request->method() === 'PATCH'
            && $request['name'] === 'Updated';
    });
});

it('sends DELETE requests', function () {
    Http::fake([
        'app.nashra.ai/*' => Http::response([
            'data' => [],
            'message' => 'Deleted.',
            'meta' => ['version' => 'v1', 'timestamp' => now()->toISOString()],
        ]),
    ]);

    $result = $this->client->delete('/subscribers/abc');

    expect($result['message'])->toBe('Deleted.');
});

it('throws AuthenticationException on 401', function () {
    Http::fake([
        'app.nashra.ai/*' => Http::response([
            'error' => ['message' => 'Unauthenticated.', 'code' => 'INVALID_TOKEN'],
            'meta' => ['version' => 'v1', 'timestamp' => now()->toISOString()],
        ], 401),
    ]);

    $this->client->get('/subscribers');
})->throws(AuthenticationException::class, 'Unauthenticated.');

it('throws AuthorizationException on 403', function () {
    Http::fake([
        'app.nashra.ai/*' => Http::response([
            'error' => ['message' => 'No newsletter configured.', 'code' => 'MISSING_NEWSLETTER'],
            'meta' => ['version' => 'v1', 'timestamp' => now()->toISOString()],
        ], 403),
    ]);

    $this->client->get('/subscribers');
})->throws(AuthorizationException::class, 'No newsletter configured.');

it('throws NotFoundException on 404', function () {
    Http::fake([
        'app.nashra.ai/*' => Http::response([
            'error' => ['message' => 'Resource not found', 'code' => 'NOT_FOUND'],
            'meta' => ['version' => 'v1', 'timestamp' => now()->toISOString()],
        ], 404),
    ]);

    $this->client->get('/subscribers/nonexistent');
})->throws(NotFoundException::class, 'Resource not found');

it('throws ValidationException on 422 with field errors', function () {
    Http::fake([
        'app.nashra.ai/*' => Http::response([
            'error' => [
                'message' => 'The given data was invalid.',
                'code' => 'VALIDATION_ERROR',
                'details' => ['email' => ['The email field is required.']],
            ],
            'meta' => ['version' => 'v1', 'timestamp' => now()->toISOString()],
        ], 422),
    ]);

    try {
        $this->client->post('/subscribers', []);
    } catch (ValidationException $e) {
        expect($e->errors())->toBe(['email' => ['The email field is required.']]);
        expect($e->statusCode)->toBe(422);

        return;
    }

    $this->fail('Expected ValidationException was not thrown.');
});

it('throws RateLimitException on 429 with retry-after', function () {
    Http::fake([
        'app.nashra.ai/*' => Http::response([
            'error' => ['message' => 'Too many requests', 'code' => 'RATE_LIMITED'],
            'meta' => ['version' => 'v1', 'timestamp' => now()->toISOString()],
        ], 429, ['Retry-After' => '30']),
    ]);

    try {
        $this->client->get('/subscribers');
    } catch (RateLimitException $e) {
        expect($e->retryAfter())->toBe(30);
        expect($e->statusCode)->toBe(429);

        return;
    }

    $this->fail('Expected RateLimitException was not thrown.');
});

it('throws ApiException on 500', function () {
    Http::fake([
        'app.nashra.ai/*' => Http::response([
            'error' => ['message' => 'Internal server error', 'code' => 'SERVER_ERROR'],
            'meta' => ['version' => 'v1', 'timestamp' => now()->toISOString()],
        ], 500),
    ]);

    $this->client->get('/subscribers');
})->throws(ApiException::class, 'Internal server error');
