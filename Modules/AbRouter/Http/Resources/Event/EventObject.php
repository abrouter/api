<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Resources\Event;

use JsonApi\JsonApi\Base\BaseObject;
use JsonApi\JsonApi\Elements\AttributesObject;
use JsonApi\JsonApi\Elements\Relationship;
use JsonApi\JsonApi\Elements\RelationshipsCollection;
use JsonApi\JsonApi\Elements\ResourceIdentifier;
use JsonApi\JsonApi\Elements\ResourceLinkage;
use JsonApi\JsonApi\Elements\ResourceObject;
use Modules\AbRouter\Models\Events\Event;

/**
 * Class EventObject
 * @property Event $model
 */
class EventObject extends BaseObject
{
    public function getInstance(): ResourceObject
    {
        $resource = new ResourceObject(
            (string) $this->model->getEntityId(),
            $this->model::getType()
        );

        $attributes = new AttributesObject([
            'user_id' => $this->model->user_id,
            'event' => $this->model->event,
            'tag' => $this->model->tag,
            'referrer' => $this->model->referrer,
            'ip' => $this->model->ip,
            'meta' => $this->model->meta,
            'created_at' => $this->model->created_at
        ]);


        $relationships = new RelationshipsCollection([
            'owner' => new Relationship(new ResourceLinkage(new ResourceIdentifier(
                $this->model->owner->getEntityId(),
                'users'
            ))),
        ]);

        return $resource
            ->withAttributes($attributes)
            ->withRelationships($relationships);
    }
}
