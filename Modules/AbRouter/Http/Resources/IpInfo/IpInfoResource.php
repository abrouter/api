<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Resources\IpInfo;

use JsonApi\JsonApi\Elements\JsonApi;
use JsonApi\JsonApi\Factories\ObjectFactory;
use JsonApi\JsonApi\Responses\JsonResource;

class IpInfoResource extends JsonResource
{
    public function jsonApiRoot(): JsonApi
    {
        return (new JsonApi(
            (new ObjectFactory())->get(IpInfoObject::class, $this->resource)
        ));
    }
}
