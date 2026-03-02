<?php

namespace Nashra\Sdk\Resources;

use Nashra\Sdk\DataObjects\Segment;

class SegmentResource extends BaseResource
{
    /**
     * @return Segment[]
     */
    public function list(): array
    {
        $response = $this->client->get('/segments');

        return array_map(
            fn (array $item) => Segment::fromArray($item),
            $response['data'] ?? [],
        );
    }

    public function find(string $uuid): Segment
    {
        $response = $this->client->get("/segments/{$uuid}");

        return Segment::fromArray($response['data']);
    }
}
