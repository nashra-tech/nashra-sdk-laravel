<?php

namespace Nashra\Sdk\Facades;

use Illuminate\Support\Facades\Facade;
use Nashra\Sdk\Resources\CustomFieldResource;
use Nashra\Sdk\Resources\SegmentResource;
use Nashra\Sdk\Resources\SubscriberResource;
use Nashra\Sdk\Resources\TagResource;

/**
 * @method static SubscriberResource subscribers()
 * @method static TagResource tags()
 * @method static CustomFieldResource fields()
 * @method static SegmentResource segments()
 *
 * @see \Nashra\Sdk\Nashra
 */
class Nashra extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Nashra\Sdk\Nashra::class;
    }
}
