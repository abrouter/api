<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Resources\ExperimentBranch;

use JsonApi\JsonApi\Base\BaseObject;
use JsonApi\JsonApi\Elements\AttributesObject;
use JsonApi\JsonApi\Elements\Relationship;
use JsonApi\JsonApi\Elements\RelationshipsCollection;
use JsonApi\JsonApi\Elements\ResourceIdentifier;
use JsonApi\JsonApi\Elements\ResourceLinkage;
use JsonApi\JsonApi\Elements\ResourceObject;
use Modules\AbRouter\Models\Experiments\Experiment;
use Modules\AbRouter\Models\Experiments\ExperimentBranches;
use Modules\Auth\Entities\AccessToken\UserWithAccessToken;

/**
 * Class UserObject
 * @package Modules\Auth\Http\Resources\User
 * @property ExperimentBranches $model
 */
class ExperimentBranchObject extends BaseObject
{
    public function getInstance(): ResourceObject
    {
        $resource = new ResourceObject(
            (string) $this->model->getEntityId(),
            $this->model::getType()
        );

        $attributes = new AttributesObject([
            'name' => $this->model->name,
            'uid' => $this->model->uid,
            'percent' => $this->model->percent,
            'config' => json_decode($this->model->config, true),
        ]);

        $relationships = new RelationshipsCollection([
            'experiment' => new Relationship(new ResourceLinkage(new ResourceIdentifier(
                $this->model->experiment->getEntityId(),
                'users'
            )))
        ]);

        return $resource
            ->withAttributes($attributes)
            ->withRelationships($relationships);
    }
}
