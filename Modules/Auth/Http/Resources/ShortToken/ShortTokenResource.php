<?php
declare(strict_types=1);

namespace Modules\Auth\Http\Resources\ShortToken;

use JsonApi\JsonApi\Elements\JsonApi;
use JsonApi\JsonApi\Factories\ObjectFactory;
use JsonApi\JsonApi\Responses\JsonResource;
use Laravel\Passport\Token;

/**
 * Class AccessTokenResource
 * @package Modules\Auth\Http\Resources\AccessToken
 * @property Token $resource
 */
class ShortTokenResource extends JsonResource
{
    public function jsonApiRoot(): JsonApi
    {
        return new JsonApi(
            (new ObjectFactory())->get(ShortTokenObject::class, $this->resource)
        );
    }
}
