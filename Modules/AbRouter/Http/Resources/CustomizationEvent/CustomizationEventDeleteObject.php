<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Resources\CustomizationEvent;

use JsonApi\JsonApi\Base\BaseObject;
use JsonApi\JsonApi\Elements\AttributesObject;
use JsonApi\JsonApi\Elements\Relationship;
use JsonApi\JsonApi\Elements\RelationshipsCollection;
use JsonApi\JsonApi\Elements\ResourceIdentifier;
use JsonApi\JsonApi\Elements\ResourceLinkage;
use JsonApi\JsonApi\Elements\ResourceObject;
use Modules\AbRouter\Models\CustomizationEvent\DisplayUserEvent;
use Modules\Auth\Exposable\AuthDecorator;

/**
 * Class CustomizationEventObject
 * @property DisplayUserEvent $model
 */
class CustomizationEventDeleteObject extends BaseObject
{
    public function getInstance(): ResourceObject
    {
        $resource = new ResourceObject(
            $this->model->getEntityId(),
            $this->model::getType()
        );

        $attributes = new AttributesObject([
            'delete' => true
        ]);


        $relationships = new RelationshipsCollection([
            'user_id' => new Relationship(new ResourceLinkage(new ResourceIdentifier(
                $this->authDecorator->get()->getId(),
                'users'
            ))),
        ]);

        return $resource
            ->withAttributes($attributes)
            ->withRelationships($relationships);
    }
}
