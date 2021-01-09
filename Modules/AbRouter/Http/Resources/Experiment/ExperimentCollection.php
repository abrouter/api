<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Resources\Experiment;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use JsonApi\JsonApi\Elements\JsonApi;
use JsonApi\JsonApi\Elements\MetaObject;
use JsonApi\JsonApi\Factories\ObjectFactory;
use JsonApi\JsonApi\Responses\JsonCollection;
use Laravel\Passport\Token;
use Modules\AbRouter\Models\Experiments\Experiment;
use Modules\AbRouter\Http\Resources\ExperimentBranch\ExperimentBranchObject;
use Modules\Auth\Exposable\AuthDecorator;

/**
 * Class UserSearchCollection
 * @package Modules\ProxiedMail\Http\Resources\ProxyBinding
 * @property Collection $collection
 */
class ExperimentCollection extends JsonCollection
{
    /**
     * @return JsonApi
     * @throws BindingResolutionException
     */
    public function jsonApiRoot(): JsonApi
    {
        /**
         * @var AuthDecorator $authDecorator
         */
        $authDecorator = app()->make(AuthDecorator::class);

        $objectCollection = (new ObjectFactory())->get(ExperimentObject::class, $this->collection);
        return (new JsonApi($objectCollection))->withMeta(new MetaObject([
            'token' => (new Token())->newQuery()->where('user_id', $authDecorator->get()->getId())->first()->id,
        ]));
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
