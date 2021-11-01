<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Resources\DisplayUserEvent;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use JsonApi\JsonApi\Elements\JsonApi;
use JsonApi\JsonApi\Factories\ObjectFactory;
use JsonApi\JsonApi\Responses\JsonCollection;
use Modules\AbRouter\Models\CustomizationEvent\DisplayUserEvent;
use Modules\Auth\Exposable\AuthDecorator;

/**
 * Class UserSearchCollection
 * @package Modules\ProxiedMail\Http\Resources\ProxyBinding
 * @property Collection $collection
 */
class DisplayUserEventCollection extends JsonCollection
{
    /**
     * @return JsonApi
     * @throws BindingResolutionException
     */
    public function jsonApiRoot(): JsonApi
    {
        /**
         * @var AuthDecorator $authDecorator
         */
        $authDecorator = app()->make(AuthDecorator::class);

        $objectCollection = (new ObjectFactory())->get(DisplayUserEventObject::class, $this->collection);
        return (new JsonApi($objectCollection));
    }
}
