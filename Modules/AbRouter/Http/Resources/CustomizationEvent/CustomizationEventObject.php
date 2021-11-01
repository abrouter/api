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
use Modules\Core\EntityId\Encoder;
use Modules\AbRouter\Models\CustomizationEvent\DisplayUserEvent;

/**
 * Class CustomizationEventObject
 * @property DisplayUserEvent $model
 */
class CustomizationEventObject extends BaseObject
{
    public function getInstance(): ResourceObject
    {
        $resource = new ResourceObject(
            (string) $this->model->getEntityId(),
            $this->model::getType()
        );

        $attributes = new AttributesObject([
            'event_name' => $this->model->event_name,
        ]);


        $relationships = new RelationshipsCollection([
            'user_id' => new Relationship(new ResourceLinkage(new ResourceIdentifier(
                $this->model->userId->getEntityId(),
                'users'
            ))),
        ]);

        return $resource
            ->withAttributes($attributes)
            ->withRelationships($relationships);
    }
}
