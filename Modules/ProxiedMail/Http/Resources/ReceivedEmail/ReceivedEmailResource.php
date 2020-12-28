<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Http\Resources\ReceivedEmail;

use JsonApi\JsonApi\Elements\JsonApi;
use JsonApi\JsonApi\Factories\ObjectFactory;
use JsonApi\JsonApi\Responses\JsonResource;
use Modules\ProxiedMail\Models\ProxyBinding;

/**
 * @property ProxyBinding $resource
 */
class ReceivedEmailResource extends JsonResource
{
    public function jsonApiRoot(): JsonApi
    {
        return new JsonApi(
            (new ObjectFactory())->get(ReceivedEmailObject::class, $this->resource)
        );
    }
}
