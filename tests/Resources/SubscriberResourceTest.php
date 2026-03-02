<?php

use Illuminate\Support\Facades\Http;
use Nashra\Sdk\DataObjects\Subscriber;
use Nashra\Sdk\Nashra;

beforeEach(function () {
    $this->nashra = app(Nashra::class);
});

it('lists subscribers with pagination', function () {
    Http::fake([
        'app.nashra.ai/api/v1/subscribers*' => Http::response([
            'data' => [
                [
                    'uuid' => 'sub-1',
                    'email' => 'john@example.com',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'status' => 'subscribed',
                    'tags' => [['uuid' => 'tag-1', 'name' => 'VIP', 'color' => '#FF0000', 'created_at' => '2026-01-01T00:00:00Z']],
                    'extra_attributes' => ['company' => 'Acme'],
                    'notes' => null,
                    'created_at' => '2026-01-01T00:00:00Z',
                    'updated_at' => '2026-01-01T00:00:00Z',
                ],
            ],
            'meta' => [
                'version' => 'v1',
                'timestamp' => '2026-01-01T00:00:00Z',
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 15,
                'total' => 1,
            ],
        ]),
    ]);

    $result = $this->nashra->subscribers()->list(['search' => 'john']);

    expect($result->items())->toHaveCount(1);
    expect($result->items()[0])->toBeInstanceOf(Subscriber::class);
    expect($result->items()[0]->email)->toBe('john@example.com');
    expect($result->items()[0]->firstName)->toBe('John');
    expect($result->items()[0]->tags)->toHaveCount(1);
    expect($result->items()[0]->tags[0]->name)->toBe('VIP');
    expect($result->total())->toBe(1);
    expect($result->hasMorePages())->toBeFalse();
});

it('creates a subscriber', function () {
    Http::fake([
        'app.nashra.ai/api/v1/subscribers' => Http::response([
            'data' => [
                'uuid' => 'sub-new',
                'email' => 'jane@example.com',
                'first_name' => 'Jane',
                'last_name' => null,
                'status' => 'subscribed',
                'tags' => [],
                'extra_attributes' => null,
                'notes' => null,
                'created_at' => '2026-01-01T00:00:00Z',
                'updated_at' => '2026-01-01T00:00:00Z',
            ],
            'meta' => ['version' => 'v1', 'timestamp' => '2026-01-01T00:00:00Z'],
        ], 201),
    ]);

    $subscriber = $this->nashra->subscribers()->create([
        'email' => 'jane@example.com',
        'first_name' => 'Jane',
    ]);

    expect($subscriber)->toBeInstanceOf(Subscriber::class);
    expect($subscriber->email)->toBe('jane@example.com');
    expect($subscriber->uuid)->toBe('sub-new');
});

it('finds a subscriber by uuid', function () {
    Http::fake([
        'app.nashra.ai/api/v1/subscribers/sub-1' => Http::response([
            'data' => [
                'uuid' => 'sub-1',
                'email' => 'john@example.com',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'status' => 'subscribed',
                'tags' => [],
                'extra_attributes' => null,
                'notes' => null,
                'created_at' => '2026-01-01T00:00:00Z',
                'updated_at' => '2026-01-01T00:00:00Z',
            ],
            'meta' => ['version' => 'v1', 'timestamp' => '2026-01-01T00:00:00Z'],
        ]),
    ]);

    $subscriber = $this->nashra->subscribers()->find('sub-1');

    expect($subscriber->uuid)->toBe('sub-1');
    expect($subscriber->email)->toBe('john@example.com');
});

it('updates a subscriber', function () {
    Http::fake([
        'app.nashra.ai/api/v1/subscribers/sub-1' => Http::response([
            'data' => [
                'uuid' => 'sub-1',
                'email' => 'john@example.com',
                'first_name' => 'Jonathan',
                'last_name' => 'Doe',
                'status' => 'subscribed',
                'tags' => [],
                'extra_attributes' => null,
                'notes' => 'Updated',
                'created_at' => '2026-01-01T00:00:00Z',
                'updated_at' => '2026-01-02T00:00:00Z',
            ],
            'meta' => ['version' => 'v1', 'timestamp' => '2026-01-02T00:00:00Z'],
        ]),
    ]);

    $subscriber = $this->nashra->subscribers()->update('sub-1', [
        'first_name' => 'Jonathan',
        'notes' => 'Updated',
    ]);

    expect($subscriber->firstName)->toBe('Jonathan');
    expect($subscriber->notes)->toBe('Updated');
});

it('deletes a subscriber', function () {
    Http::fake([
        'app.nashra.ai/api/v1/subscribers/sub-1' => Http::response([
            'data' => [],
            'message' => 'Subscriber deleted.',
            'meta' => ['version' => 'v1', 'timestamp' => '2026-01-01T00:00:00Z'],
        ]),
    ]);

    $result = $this->nashra->subscribers()->delete('sub-1');

    expect($result['message'])->toBe('Subscriber deleted.');
});

it('auto-paginates through all pages', function () {
    Http::fake([
        'app.nashra.ai/api/v1/subscribers?page=1' => Http::response([
            'data' => [
                ['uuid' => 'sub-1', 'email' => 'a@e.com', 'first_name' => null, 'last_name' => null, 'status' => 'subscribed', 'tags' => [], 'extra_attributes' => null, 'notes' => null, 'created_at' => null, 'updated_at' => null],
            ],
            'meta' => ['version' => 'v1', 'timestamp' => '2026-01-01T00:00:00Z', 'current_page' => 1, 'last_page' => 2, 'per_page' => 1, 'total' => 2],
        ]),
        'app.nashra.ai/api/v1/subscribers?page=2' => Http::response([
            'data' => [
                ['uuid' => 'sub-2', 'email' => 'b@e.com', 'first_name' => null, 'last_name' => null, 'status' => 'subscribed', 'tags' => [], 'extra_attributes' => null, 'notes' => null, 'created_at' => null, 'updated_at' => null],
            ],
            'meta' => ['version' => 'v1', 'timestamp' => '2026-01-01T00:00:00Z', 'current_page' => 2, 'last_page' => 2, 'per_page' => 1, 'total' => 2],
        ]),
    ]);

    $emails = [];
    foreach ($this->nashra->subscribers()->list() as $subscriber) {
        $emails[] = $subscriber->email;
    }

    expect($emails)->toBe(['a@e.com', 'b@e.com']);
});
