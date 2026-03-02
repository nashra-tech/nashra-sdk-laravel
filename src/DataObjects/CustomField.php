<?php

namespace Nashra\Sdk\DataObjects;

final readonly class CustomField
{
    public function __construct(
        public string $uuid,
        public string $fieldName,
        public string $dataName,
        public string $type,
        public bool $isRequired,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            uuid: $data['uuid'],
            fieldName: $data['field_name'],
            dataName: $data['data_name'],
            type: $data['type'],
            isRequired: $data['is_required'] ?? false,
        );
    }
}
