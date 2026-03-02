<?php

use Illuminate\Support\Facades\Http;
use Nashra\Sdk\DataObjects\Segment;
use Nashra\Sdk\Nashra;

beforeEach(function () {
    $this->nashra = app(Nashra::class);
});

it('lists all segments', function () {
    Http::fake([
        'app.nashra.ai/api/v1/segments' => Http::response([
            'data' => [
                ['uuid' => 'seg-1', 'name' => 'Active Users', 'subscribers_count' => 150, 'stored_conditions' => [['field' => 'status', 'operator' => '=', 'value' => 'active']], 'created_at' => '2026-01-01T00:00:00Z'],
                ['uuid' => 'seg-2', 'name' => 'New Users', 'subscribers_count' => 50, 'stored_conditions' => [], 'created_at' => '2026-01-01T00:00:00Z'],
            ],
            'meta' => ['version' => 'v1', 'timestamp' => '2026-01-01T00:00:00Z'],
        ]),
    ]);

    $segments = $this->nashra->segments()->list();

    expect($segments)->toHaveCount(2);
    expect($segments[0])->toBeInstanceOf(Segment::class);
    expect($segments[0]->name)->toBe('Active Users');
    expect($segments[0]->subscribersCount)->toBe(150);
    expect($segments[1]->name)->toBe('New Users');
});

it('finds a segment by uuid', function () {
    Http::fake([
        'app.nashra.ai/api/v1/segments/seg-1' => Http::response([
            'data' => [
                'uuid' => 'seg-1',
                'name' => 'Active Users',
                'subscribers_count' => 150,
                'stored_conditions' => [['field' => 'status', 'operator' => '=', 'value' => 'active']],
                'created_at' => '2026-01-01T00:00:00Z',
            ],
            'meta' => ['version' => 'v1', 'timestamp' => '2026-01-01T00:00:00Z'],
        ]),
    ]);

    $segment = $this->nashra->segments()->find('seg-1');

    expect($segment)->toBeInstanceOf(Segment::class);
    expect($segment->uuid)->toBe('seg-1');
    expect($segment->name)->toBe('Active Users');
    expect($segment->storedConditions)->toBeArray();
});
