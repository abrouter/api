<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Resources\AddUserToExperiment;

use JsonApi\JsonApi\Base\BaseObject;
use JsonApi\JsonApi\Elements\AttributesObject;
use JsonApi\JsonApi\Elements\Relationship;
use JsonApi\JsonApi\Elements\RelationshipsCollection;
use JsonApi\JsonApi\Elements\ResourceIdentifier;
use JsonApi\JsonApi\Elements\ResourceLinkage;
use JsonApi\JsonApi\Elements\ResourceObject;
use Modules\AbRouter\Models\Experiments\ExperimentBranchUser;

/**
 * Class UserObject
 * @package Modules\Auth\Http\Resources\User
 * @property ExperimentBranchUser $model
 */
class AddUserToExperimentObject extends BaseObject
{
    public function getInstance(): ResourceObject
    {
        $resource = new ResourceObject(
            (string) $this->model->getEntityId(),
            $this->model::getType()
        );

        $attributes = new AttributesObject([
            'user_added' => (bool) $this->model
        ]);

        return $resource
            ->withAttributes($attributes);
    }
}
