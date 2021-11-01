<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Resources\CustomizationEvent;

use JsonApi\JsonApi\Elements\JsonApi;
use JsonApi\JsonApi\Factories\ObjectFactory;
use JsonApi\JsonApi\Responses\JsonResource;
use Modules\AbRouter\Models\CustomizationEvent\DisplayUserEvent;

/**
 * Class AccessTokenResource
 * @property DisplayUserEvent resource
 */
class CustomizationEventDeleteResource extends JsonResource
{
    public function jsonApiRoot(): JsonApi
    {
        return (new JsonApi(
            (new ObjectFactory())->get(CustomizationEventDeleteObject::class, $this->resource)
        ));
    }
}
