<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Resources\ExperimentBranchUser;

use JsonApi\JsonApi\Base\BaseObject;
use JsonApi\JsonApi\Elements\AttributesObject;
use JsonApi\JsonApi\Elements\Relationship;
use JsonApi\JsonApi\Elements\RelationshipsCollection;
use JsonApi\JsonApi\Elements\ResourceIdentifier;
use JsonApi\JsonApi\Elements\ResourceLinkage;
use JsonApi\JsonApi\Elements\ResourceObject;
use Modules\AbRouter\Models\Experiments\Experiment;
use Modules\AbRouter\Models\Experiments\ExperimentBranchUser;
use Modules\Auth\Entities\AccessToken\UserWithAccessToken;

/**
 * Class UserObject
 * @property ExperimentBranchUser $model
 */
class ExperimentBranchUserObject extends BaseObject
{
    public function getInstance(): ResourceObject
    {
        $resource = new ResourceObject(
            (string) $this->model->getEntityId(),
            $this->model::getType()
        );

        $uid = sprintf('%s-%s', $this->model->experiment->uid, $this->model->experimentBranch->uid);
        $attributes = new AttributesObject([
            'run-uid' => $uid,
        ]);

        $relationships = new RelationshipsCollection([
            'experiment_user' => new Relationship(new ResourceLinkage(new ResourceIdentifier(
                $this->model->experimentUser->getEntityId(),
                'experiment_user'
            ))),
            'experiment_id' => new Relationship(new ResourceLinkage(new ResourceIdentifier(
                $this->model->experiment->getEntityId(),
                'users'
            ))),
            'experiment_branch_id' => new Relationship(new ResourceLinkage(new ResourceIdentifier(
                $this->model->experimentBranch->getEntityId(),
                'users'
            ))),
        ]);

        return $resource
            ->withAttributes($attributes)
            ->withRelationships($relationships);
    }
}
