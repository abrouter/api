<?php
declare(strict_types=1);

namespace Modules\Auth\Http\Resources\AccessToken;

use JsonApi\JsonApi\Elements\JsonApi;
use JsonApi\JsonApi\Factories\ObjectFactory;
use JsonApi\JsonApi\Responses\JsonResource;
use Modules\Auth\Entities\AccessToken\UserWithAccessToken;

/**
 * Class AccessTokenResource
 * @package Modules\Auth\Http\Resources\AccessToken
 * @property UserWithAccessToken
 */
class AccessTokenResource extends JsonResource
{
    public function jsonApiRoot(): JsonApi
    {
        return new JsonApi(
            (new ObjectFactory())->get(AccessTokenObject::class, $this->resource)
        );
    }
}
