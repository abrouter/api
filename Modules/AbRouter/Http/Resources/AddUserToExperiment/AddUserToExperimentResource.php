<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Resources\AddUserToExperiment;

use JsonApi\JsonApi\Elements\JsonApi;
use JsonApi\JsonApi\Factories\ObjectFactory;
use JsonApi\JsonApi\Responses\JsonResource;

/**
 * Class AddUserToExperimentResource
 * @property resource ExperimentBranchUser
 */
class AddUserToExperimentResource extends JsonResource
{
    public function jsonApiRoot(): JsonApi
    {
        return (new JsonApi(
            (new ObjectFactory())->get(AddUserToExperimentObject::class, $this->resource)
        ));
    }
}
