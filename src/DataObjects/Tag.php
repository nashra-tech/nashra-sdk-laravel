<?php

namespace Nashra\Sdk\DataObjects;

final readonly class Tag
{
    public function __construct(
        public string $uuid,
        public string $name,
        public ?string $color,
        public ?string $createdAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            uuid: $data['uuid'],
            name: $data['name'],
            color: $data['color'] ?? null,
            createdAt: $data['created_at'] ?? null,
        );
    }
}
