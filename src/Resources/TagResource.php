<?php

namespace Nashra\Sdk\Resources;

use Nashra\Sdk\DataObjects\PaginatedResponse;
use Nashra\Sdk\DataObjects\Tag;

class TagResource extends BaseResource
{
    /**
     * @param  array{per_page?: int, page?: int}  $params
     * @return PaginatedResponse<Tag>
     */
    public function list(array $params = []): PaginatedResponse
    {
        $fetchPage = function (int $page) use ($params, &$fetchPage): PaginatedResponse {
            $query = array_merge($params, ['page' => $page]);
            $response = $this->client->get('/tags', $query);

            $items = array_map(
                fn (array $item) => Tag::fromArray($item),
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

    public function create(array $data): Tag
    {
        $response = $this->client->post('/tags', $data);

        return Tag::fromArray($response['data']);
    }

    public function update(string $uuid, array $data): Tag
    {
        $response = $this->client->patch("/tags/{$uuid}", $data);

        return Tag::fromArray($response['data']);
    }

    public function delete(string $uuid): array
    {
        return $this->client->delete("/tags/{$uuid}");
    }
}
