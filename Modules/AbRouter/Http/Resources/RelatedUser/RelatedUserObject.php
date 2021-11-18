<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Resources\RelatedUser;

use JsonApi\JsonApi\Base\BaseObject;
use JsonApi\JsonApi\Elements\AttributesObject;
use JsonApi\JsonApi\Elements\Relationship;
use JsonApi\JsonApi\Elements\RelationshipsCollection;
use JsonApi\JsonApi\Elements\ResourceIdentifier;
use JsonApi\JsonApi\Elements\ResourceLinkage;
use JsonApi\JsonApi\Elements\ResourceObject;
use Modules\Core\EntityId\Encoder;
use Modules\AbRouter\Models\RelatedUser\RelatedUser;

/**
 * Class RelatedUserObject
 * @property RelatedUser $model
 */
class RelatedUserObject extends BaseObject
{
    public function getInstance(): ResourceObject
    {
        $resource = new ResourceObject(
            (string) $this->model->getEntityId(),
            $this->model::getType()
        );

        $attributes = new AttributesObject([
            'user_id' => $this->model->user_id,
            'related_user_id' => $this->model->related_user_id
        ]);


        $relationships = new RelationshipsCollection([
            'owner_id' => new Relationship(new ResourceLinkage(new ResourceIdentifier(
                $this->model->owner->getEntityId(),
                'users'
            ))),
        ]);

        return $resource
            ->withAttributes($attributes)
            ->withRelationships($relationships);
    }
}
