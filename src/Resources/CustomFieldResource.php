<?php

namespace Nashra\Sdk\Resources;

use Nashra\Sdk\DataObjects\CustomField;
use Nashra\Sdk\DataObjects\PaginatedResponse;

class CustomFieldResource extends BaseResource
{
    /**
     * @param  array{per_page?: int, page?: int}  $params
     * @return PaginatedResponse<CustomField>
     */
    public function list(array $params = []): PaginatedResponse
    {
        $fetchPage = function (int $page) use ($params, &$fetchPage): PaginatedResponse {
            $query = array_merge($params, ['page' => $page]);
            $response = $this->client->get('/fields', $query);

            $items = array_map(
                fn (array $item) => CustomField::fromArray($item),
                $response['data'] ?? [],
            );

            return new PaginatedResponse(
                items: $items,
                currentPage: $response['meta']['current_page'] ?? $page,
                lastPage: $response['meta']['last_page'] ?? 1,
                total: $response['meta']['total'] ?? count($items),
                perPage: $response['meta']['per_page'] ?? 15,
                fetcher: $fetchPage,
            );
        };

        $page = $params['page'] ?? 1;

        return $fetchPage($page);
    }

    public function create(array $data): CustomField
    {
        $response = $this->client->post('/fields', $data);

        return CustomField::fromArray($response['data']);
    }

    public function update(string $uuid, array $data): CustomField
    {
        $response = $this->client->patch("/fields/{$uuid}", $data);

        return CustomField::fromArray($response['data']);
    }

    public function delete(string $uuid): array
    {
        return $this->client->delete("/fields/{$uuid}");
    }
}
