<?php

use Nashra\Sdk\DataObjects\Subscriber;
use Nashra\Sdk\DataObjects\Tag;

it('creates a subscriber from array', function () {
    $data = [
        'uuid' => 'sub-1',
        'email' => 'john@example.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'status' => 'subscribed',
        'tags' => [
            ['uuid' => 'tag-1', 'name' => 'VIP', 'color' => '#FF0000', 'created_at' => '2026-01-01T00:00:00Z'],
        ],
        'extra_attributes' => ['company' => 'Acme'],
        'notes' => 'Important subscriber',
        'created_at' => '2026-01-01T00:00:00Z',
        'updated_at' => '2026-01-02T00:00:00Z',
    ];

    $subscriber = Subscriber::fromArray($data);

    expect($subscriber)->toBeInstanceOf(Subscriber::class);
    expect($subscriber->uuid)->toBe('sub-1');
    expect($subscriber->email)->toBe('john@example.com');
    expect($subscriber->firstName)->toBe('John');
    expect($subscriber->lastName)->toBe('Doe');
    expect($subscriber->status)->toBe('subscribed');
    expect($subscriber->tags)->toHaveCount(1);
    expect($subscriber->tags[0])->toBeInstanceOf(Tag::class);
    expect($subscriber->tags[0]->name)->toBe('VIP');
    expect($subscriber->extraAttributes)->toBe(['company' => 'Acme']);
    expect($subscriber->notes)->toBe('Important subscriber');
});

it('handles nullable fields gracefully', function () {
    $data = [
        'uuid' => 'sub-2',
        'email' => 'minimal@example.com',
    ];

    $subscriber = Subscriber::fromArray($data);

    expect($subscriber->uuid)->toBe('sub-2');
    expect($subscriber->email)->toBe('minimal@example.com');
    expect($subscriber->firstName)->toBeNull();
    expect($subscriber->lastName)->toBeNull();
    expect($subscriber->status)->toBeNull();
    expect($subscriber->tags)->toBe([]);
    expect($subscriber->extraAttributes)->toBeNull();
    expect($subscriber->notes)->toBeNull();
});
