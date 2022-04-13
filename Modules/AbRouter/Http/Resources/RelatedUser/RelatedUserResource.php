<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Resources\RelatedUser;

use JsonApi\JsonApi\Elements\JsonApi;
use JsonApi\JsonApi\Factories\ObjectFactory;
use JsonApi\JsonApi\Responses\JsonResource;
use Modules\AbRouter\Models\RelatedUsers\RelatedUser;

/**
 * Class AccessTokenResource
 * @property RelatedUser resource
 */
class RelatedUserResource extends JsonResource
{
    public function jsonApiRoot(): JsonApi
    {
        return (new JsonApi(
            (new ObjectFactory())->get(RelatedUserObject::class, $this->resource)
        ));
    }
}
