<?php

namespace Nashra\Sdk\Resources;

use Nashra\Sdk\DataObjects\PaginatedResponse;
use Nashra\Sdk\DataObjects\Subscriber;

class SubscriberResource extends BaseResource
{
    /**
     * @param  array{per_page?: int, page?: int, search?: string, status?: string}  $params
     * @return PaginatedResponse<Subscriber>
     */
    public function list(array $params = []): PaginatedResponse
    {
        $fetchPage = function (int $page) use ($params, &$fetchPage): PaginatedResponse {
            $query = array_merge($params, ['page' => $page]);
            $response = $this->client->get('/subscribers', $query);

            $items = array_map(
                fn (array $item) => Subscriber::fromArray($item),
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

    public function create(array $data): Subscriber
    {
        $response = $this->client->post('/subscribers', $data);

        return Subscriber::fromArray($response['data']);
    }

    public function find(string $uuid): Subscriber
    {
        $response = $this->client->get("/subscribers/{$uuid}");

        return Subscriber::fromArray($response['data']);
    }

    public function update(string $uuid, array $data): Subscriber
    {
        $response = $this->client->patch("/subscribers/{$uuid}", $data);

        return Subscriber::fromArray($response['data']);
    }

    public function delete(string $uuid): array
    {
        return $this->client->delete("/subscribers/{$uuid}");
    }
}
