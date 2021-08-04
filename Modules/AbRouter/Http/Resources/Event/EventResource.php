<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Resources\Event;

use JsonApi\JsonApi\Elements\JsonApi;
use JsonApi\JsonApi\Factories\ObjectFactory;
use JsonApi\JsonApi\Responses\JsonResource;
use Modules\AbRouter\Models\Events\Event;

/**
 * Class AccessTokenResource
 * @property Event resource
 */
class EventResource extends JsonResource
{
    public function jsonApiRoot(): JsonApi
    {
        return (new JsonApi(
            (new ObjectFactory())->get(EventObject::class, $this->resource)
        ));
    }
}
