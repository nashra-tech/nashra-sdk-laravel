<?php

namespace Nashra\Sdk\DataObjects;

final readonly class Segment
{
    public function __construct(
        public string $uuid,
        public string $name,
        public int $subscribersCount,
        public ?array $storedConditions,
        public ?string $createdAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            uuid: $data['uuid'],
            name: $data['name'],
            subscribersCount: $data['subscribers_count'] ?? 0,
            storedConditions: $data['stored_conditions'] ?? null,
            createdAt: $data['created_at'] ?? null,
        );
    }
}
