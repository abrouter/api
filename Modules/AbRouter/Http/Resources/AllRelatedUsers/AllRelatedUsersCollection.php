<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Resources\AllRelatedUsers;

use JsonApi\JsonApi\Elements\JsonApi;
use JsonApi\JsonApi\Factories\ObjectFactory;
use JsonApi\JsonApi\Responses\JsonCollection;
use Illuminate\Support\Collection;

/**
 * Class AllRelatedUsersResource
 * @property Collection $collection
 */
class AllRelatedUsersCollection extends JsonCollection
{
    public function jsonApiRoot(): JsonApi
    {
        return (new JsonApi(
            (new ObjectFactory())->get(AllRelatedUsersObject::class, $this->collection)
        ));
    }
}
