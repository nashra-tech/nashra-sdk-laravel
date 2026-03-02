<?php

use Illuminate\Support\Facades\Http;
use Nashra\Sdk\DataObjects\Tag;
use Nashra\Sdk\Nashra;

beforeEach(function () {
    $this->nashra = app(Nashra::class);
});

it('lists tags', function () {
    Http::fake([
        'app.nashra.ai/api/v1/tags*' => Http::response([
            'data' => [
                ['uuid' => 'tag-1', 'name' => 'VIP', 'color' => '#FF0000', 'created_at' => '2026-01-01T00:00:00Z'],
                ['uuid' => 'tag-2', 'name' => 'New', 'color' => '#00FF00', 'created_at' => '2026-01-01T00:00:00Z'],
            ],
            'meta' => ['version' => 'v1', 'timestamp' => '2026-01-01T00:00:00Z', 'current_page' => 1, 'last_page' => 1, 'per_page' => 15, 'total' => 2],
        ]),
    ]);

    $result = $this->nashra->tags()->list();

    expect($result->items())->toHaveCount(2);
    expect($result->items()[0])->toBeInstanceOf(Tag::class);
    expect($result->items()[0]->name)->toBe('VIP');
    expect($result->items()[1]->color)->toBe('#00FF00');
});

it('creates a tag', function () {
    Http::fake([
        'app.nashra.ai/api/v1/tags' => Http::response([
            'data' => ['uuid' => 'tag-new', 'name' => 'Important', 'color' => '#FF5733', 'created_at' => '2026-01-01T00:00:00Z'],
            'meta' => ['version' => 'v1', 'timestamp' => '2026-01-01T00:00:00Z'],
        ], 201),
    ]);

    $tag = $this->nashra->tags()->create(['name' => 'Important', 'color' => '#FF5733']);

    expect($tag)->toBeInstanceOf(Tag::class);
    expect($tag->name)->toBe('Important');
    expect($tag->color)->toBe('#FF5733');
});

it('updates a tag', function () {
    Http::fake([
        'app.nashra.ai/api/v1/tags/tag-1' => Http::response([
            'data' => ['uuid' => 'tag-1', 'name' => 'Super VIP', 'color' => '#0000FF', 'created_at' => '2026-01-01T00:00:00Z'],
            'meta' => ['version' => 'v1', 'timestamp' => '2026-01-01T00:00:00Z'],
        ]),
    ]);

    $tag = $this->nashra->tags()->update('tag-1', ['name' => 'Super VIP', 'color' => '#0000FF']);

    expect($tag->name)->toBe('Super VIP');
});

it('deletes a tag', function () {
    Http::fake([
        'app.nashra.ai/api/v1/tags/tag-1' => Http::response([
            'data' => [],
            'message' => 'Tag deleted.',
            'meta' => ['version' => 'v1', 'timestamp' => '2026-01-01T00:00:00Z'],
        ]),
    ]);

    $result = $this->nashra->tags()->delete('tag-1');

    expect($result['message'])->toBe('Tag deleted.');
});
