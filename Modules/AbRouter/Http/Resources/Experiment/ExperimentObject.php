<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Resources\Experiment;

use JsonApi\JsonApi\Base\BaseObject;
use JsonApi\JsonApi\Elements\AttributesObject;
use JsonApi\JsonApi\Elements\Relationship;
use JsonApi\JsonApi\Elements\RelationshipsCollection;
use JsonApi\JsonApi\Elements\ResourceIdentifier;
use JsonApi\JsonApi\Elements\ResourceLinkage;
use JsonApi\JsonApi\Elements\ResourceObject;
use Modules\AbRouter\Models\Experiments\Experiment;
use Modules\Auth\Entities\AccessToken\UserWithAccessToken;

/**
 * Class UserObject
 * @package Modules\Auth\Http\Resources\User
 * @property Experiment $model
 */
class ExperimentObject extends BaseObject
{
    public function getInstance(): ResourceObject
    {
        $resource = new ResourceObject(
            (string) $this->model->getEntityId(),
            $this->model::getType()
        );

        $attributes = new AttributesObject([
            'name' => $this->model->name,
            'config' => json_decode($this->model->config, true),
            'is_enabled' => $this->model->is_enabled
        ]);

        $relationships = new RelationshipsCollection([
            'owner' => new Relationship(new ResourceLinkage(new ResourceIdentifier(
                $this->model->owner->getEntityId(),
                'users'
            )))
        ]);

        return $resource
            ->withAttributes($attributes)
            ->withRelationships($relationships);
    }
}
