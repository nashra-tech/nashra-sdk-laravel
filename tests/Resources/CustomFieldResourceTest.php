<?php

use Illuminate\Support\Facades\Http;
use Nashra\Sdk\DataObjects\CustomField;
use Nashra\Sdk\Nashra;

beforeEach(function () {
    $this->nashra = app(Nashra::class);
});

it('lists custom fields', function () {
    Http::fake([
        'app.nashra.ai/api/v1/fields*' => Http::response([
            'data' => [
                ['uuid' => 'field-1', 'field_name' => 'Company', 'data_name' => 'company', 'type' => 'text', 'is_required' => false],
                ['uuid' => 'field-2', 'field_name' => 'Age', 'data_name' => 'age', 'type' => 'number', 'is_required' => true],
            ],
            'meta' => ['version' => 'v1', 'timestamp' => '2026-01-01T00:00:00Z', 'current_page' => 1, 'last_page' => 1, 'per_page' => 15, 'total' => 2],
        ]),
    ]);

    $result = $this->nashra->fields()->list();

    expect($result->items())->toHaveCount(2);
    expect($result->items()[0])->toBeInstanceOf(CustomField::class);
    expect($result->items()[0]->fieldName)->toBe('Company');
    expect($result->items()[0]->type)->toBe('text');
    expect($result->items()[1]->isRequired)->toBeTrue();
});

it('creates a custom field', function () {
    Http::fake([
        'app.nashra.ai/api/v1/fields' => Http::response([
            'data' => ['uuid' => 'field-new', 'field_name' => 'Website', 'data_name' => 'website', 'type' => 'text', 'is_required' => false],
            'meta' => ['version' => 'v1', 'timestamp' => '2026-01-01T00:00:00Z'],
        ], 201),
    ]);

    $field = $this->nashra->fields()->create([
        'field_name' => 'Website',
        'data_name' => 'website',
        'type' => 'text',
    ]);

    expect($field)->toBeInstanceOf(CustomField::class);
    expect($field->fieldName)->toBe('Website');
    expect($field->dataName)->toBe('website');
});

it('updates a custom field', function () {
    Http::fake([
        'app.nashra.ai/api/v1/fields/field-1' => Http::response([
            'data' => ['uuid' => 'field-1', 'field_name' => 'Company Name', 'data_name' => 'company', 'type' => 'text', 'is_required' => true],
            'meta' => ['version' => 'v1', 'timestamp' => '2026-01-01T00:00:00Z'],
        ]),
    ]);

    $field = $this->nashra->fields()->update('field-1', [
        'field_name' => 'Company Name',
        'is_required' => true,
    ]);

    expect($field->fieldName)->toBe('Company Name');
    expect($field->isRequired)->toBeTrue();
});

it('deletes a custom field', function () {
    Http::fake([
        'app.nashra.ai/api/v1/fields/field-1' => Http::response([
            'data' => [],
            'message' => 'Custom field deleted.',
            'meta' => ['version' => 'v1', 'timestamp' => '2026-01-01T00:00:00Z'],
        ]),
    ]);

    $result = $this->nashra->fields()->delete('field-1');

    expect($result['message'])->toBe('Custom field deleted.');
});
