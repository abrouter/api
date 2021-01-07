<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Resources\Experiment;

use JsonApi\JsonApi\Elements\JsonApi;
use JsonApi\JsonApi\Factories\ObjectFactory;
use JsonApi\JsonApi\Responses\JsonResource;

/**
 * Class AccessTokenResource
 * @property resource Experiment
 */
class ExperimentResource extends JsonResource
{
    public function jsonApiRoot(): JsonApi
    {
        return (new JsonApi(
            (new ObjectFactory())->get(ExperimentObject::class, $this->resource)
        ));
    }
}
