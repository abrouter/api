<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Http\Resources\ProxyBinding;

use Illuminate\Support\Collection;
use JsonApi\JsonApi\Elements\JsonApi;
use JsonApi\JsonApi\Factories\ObjectFactory;
use JsonApi\JsonApi\Responses\JsonCollection;

/**
 * Class UserSearchCollection
 * @package Modules\ProxiedMail\Http\Resources\ProxyBinding
 * @property Collection $collection
 */
class ProxyBindingCollection extends JsonCollection
{
    /**
     * @return JsonApi
     */
    public function jsonApiRoot(): JsonApi
    {
        $objectCollection = (new ObjectFactory())->get(ProxyBindingObject::class, $this->collection);
        return new JsonApi($objectCollection);
    }
}
