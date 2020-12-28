<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Http\Resources\ProxyBinding;

use JsonApi\JsonApi\Elements\JsonApi;
use JsonApi\JsonApi\Factories\ObjectFactory;
use JsonApi\JsonApi\Responses\JsonResource;
use Modules\Auth\Entities\AccessToken\UserWithAccessToken;
use Modules\ProxiedMail\Http\Resources\ProxyBinding\ProxyBindingObject;
use Modules\ProxiedMail\Models\ProxyBinding;

/**
 * @property ProxyBinding $resource
 */
class ProxyBindingResource extends JsonResource
{
    public function jsonApiRoot(): JsonApi
    {
        return new JsonApi(
            (new ObjectFactory())->get(ProxyBindingObject::class, $this->resource)
        );
    }
}
