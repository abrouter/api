<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Http\Resources\ProxyBinding;

use Illuminate\Support\Collection;
use JsonApi\JsonApi\Elements\JsonApi;
use JsonApi\JsonApi\Factories\ObjectFactory;
use JsonApi\JsonApi\Responses\JsonCollection;
use Modules\AbRouter\Models\Experiments\Experiment;
use Modules\AbRouter\Http\Resources\ExperimentBranch\ExperimentBranchObject;
use Modules\Auth\Http\Resources\AccessToken\ExperimentObject;

/**
 * Class UserSearchCollection
 * @package Modules\ProxiedMail\Http\Resources\ProxyBinding
 * @property Collection $collection
 */
class ExperimentCollection extends JsonCollection
{
    /**
     * @return JsonApi
     */
    public function jsonApiRoot(): JsonApi
    {
        $objectCollection = (new ObjectFactory())->get(ExperimentObject::class, $this->collection);
        return (new JsonApi($objectCollection));
    }

    protected function customIncluded(): array
    {
        $branches = $this->collection->reduce(function (Collection $acc, Experiment $experiment) {
            $acc = $acc->merge($experiment->branches);
            return $acc;
        }, collect());

        return [
            'branches' => (new ObjectFactory())->get(ExperimentBranchObject::class, $branches)
        ];
    }
}
