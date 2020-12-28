<?php
declare(strict_types=1);

namespace Modules\Auth\Http\Resources\User;

use JsonApi\JsonApi\Elements\JsonApi;
use JsonApi\JsonApi\Factories\ObjectFactory;
use JsonApi\JsonApi\Responses\JsonResource;
use Modules\Auth\Models\User\User;

/**
 * Class UserResource
 * @package Modules\Auth\Http\Resources\User
 * @property User $resource
 */
class UserResource extends JsonResource
{
    public function jsonApiRoot(): JsonApi
    {
        return new JsonApi(
            (new ObjectFactory())->get(UserObject::class, $this->resource)
        );
    }
}
