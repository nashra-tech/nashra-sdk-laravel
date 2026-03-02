<?php

namespace Nashra\Sdk\DataObjects;

final readonly class Subscriber
{
    /**
     * @param  Tag[]  $tags
     */
    public function __construct(
        public string $uuid,
        public string $email,
        public ?string $firstName,
        public ?string $lastName,
        public ?string $status,
        public array $tags,
        public ?array $extraAttributes,
        public ?string $notes,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            uuid: $data['uuid'],
            email: $data['email'],
            firstName: $data['first_name'] ?? null,
            lastName: $data['last_name'] ?? null,
            status: $data['status'] ?? null,
            tags: array_map(
                fn (array $tag) => Tag::fromArray($tag),
                $data['tags'] ?? [],
            ),
            extraAttributes: $data['extra_attributes'] ?? null,
            notes: $data['notes'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
        );
    }
}
