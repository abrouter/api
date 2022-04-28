<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Resources\ExperimentsHaveUsers;

use Illuminate\Support\Collection;
use JsonApi\JsonApi\Elements\JsonApi;
use JsonApi\JsonApi\Factories\ObjectFactory;
use JsonApi\JsonApi\Responses\JsonCollection;

/**
 * Class ExperimentsHaveUsersCollection
 * @package Modules\AbRouter\Http\Resources\ExperimentsHaveUsers
 * @property Collection $collection
 */
class ExperimentsHaveUsersCollection extends JsonCollection
{
    /**
     * @return JsonApi
     */
    public function jsonApiRoot(): JsonApi
    {
        return (new JsonApi(
            (new ObjectFactory())->get(ExperimentsHaveUsersObject::class, $this->collection)
        ));
    }
}
