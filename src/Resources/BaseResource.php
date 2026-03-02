<?php

namespace Nashra\Sdk\Resources;

use Nashra\Sdk\NashraClient;

abstract class BaseResource
{
    public function __construct(
        protected readonly NashraClient $client,
    ) {}
}
