<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Resources\Event;

use Illuminate\Support\Collection;
use JsonApi\JsonApi\Elements\JsonApi;
use JsonApi\JsonApi\Factories\ObjectFactory;
use JsonApi\JsonApi\Responses\JsonCollection;

/**
 * Class EventsCollection
 * @package Modules\AbRouter\Http\Resources\Event
 * @property Collection $collection
 */
class EventsCollection extends JsonCollection
{
    /**
     * @return JsonApi
     */
    public function jsonApiRoot(): JsonApi
    {
        return new JsonApi(
            (new ObjectFactory())->get(EventObject::class, $this->collection)
        );
    }
}
