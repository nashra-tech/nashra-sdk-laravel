<?php

namespace Nashra\Sdk;

use Nashra\Sdk\Resources\CustomFieldResource;
use Nashra\Sdk\Resources\SegmentResource;
use Nashra\Sdk\Resources\SubscriberResource;
use Nashra\Sdk\Resources\TagResource;

class Nashra
{
    private NashraClient $client;

    private ?SubscriberResource $subscriberResource = null;

    private ?TagResource $tagResource = null;

    private ?CustomFieldResource $customFieldResource = null;

    private ?SegmentResource $segmentResource = null;

    public function __construct(
        string $apiKey,
        string $baseUrl = 'https://app.nashra.ai/api/v1',
        int $timeout = 30,
        int $maxRetries = 3,
    ) {
        $this->client = new NashraClient($apiKey, $baseUrl, $timeout, $maxRetries);
    }

    public function subscribers(): SubscriberResource
    {
        return $this->subscriberResource ??= new SubscriberResource($this->client);
    }

    public function tags(): TagResource
    {
        return $this->tagResource ??= new TagResource($this->client);
    }

    public function fields(): CustomFieldResource
    {
        return $this->customFieldResource ??= new CustomFieldResource($this->client);
    }

    public function segments(): SegmentResource
    {
        return $this->segmentResource ??= new SegmentResource($this->client);
    }
}
