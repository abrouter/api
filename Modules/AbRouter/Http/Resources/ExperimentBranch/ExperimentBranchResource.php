<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Resources\ExperimentBranch;

use JsonApi\JsonApi\Elements\JsonApi;
use JsonApi\JsonApi\Elements\ResourceObjectCollection;
use JsonApi\JsonApi\Factories\ObjectFactory;
use JsonApi\JsonApi\Responses\JsonResource;
use Modules\AbRouter\Http\Resources\ExperimentBranch\ExperimentBranchObject;
use Modules\Auth\Entities\AccessToken\UserWithAccessToken;

/**
 * Class AccessTokenResource
 * @package Modules\Auth\Http\Resources\AccessToken
 * @property UserWithAccessToken
 */
class ExperimentBranchResource extends JsonResource
{
    public function jsonApiRoot(): JsonApi
    {
        return (new JsonApi(
            (new ObjectFactory())->get(ExperimentBranchObject::class, $this->resource)
        ));
    }
}
