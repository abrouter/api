<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Resources\Tag;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use JsonApi\JsonApi\Elements\JsonApi;
use JsonApi\JsonApi\Elements\MetaObject;
use JsonApi\JsonApi\Factories\ObjectFactory;
use JsonApi\JsonApi\Responses\JsonCollection;

/**
 * Class UserSearchCollection
 * @package Modules\ProxiedMail\Http\Resources\ProxyBinding
 * @property Collection $collection
 */
class TagCollection extends JsonCollection
{
    /**
     * @return JsonApi
     * @throws BindingResolutionException
     */
    public function jsonApiRoot(): JsonApi
    {
        $objectCollection = (new ObjectFactory())->get(TagObject::class, $this->collection);
        return (new JsonApi($objectCollection));
    }
}
