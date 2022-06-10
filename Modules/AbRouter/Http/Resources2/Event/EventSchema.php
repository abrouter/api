<?php
declare(strict_types = 1);

namespace Modules\AbRouter\Http\Resources2\Event;

use AbRouter\JsonApiFormatter\Document\Collections\RelationshipsCollection;
use AbRouter\JsonApiFormatter\Document\Schema\DocumentSchema;
use AbRouter\JsonApiFormatter\Document\Sections\Attributes;
use AbRouter\JsonApiFormatter\Document\Sections\Identifier;
use AbRouter\JsonApiFormatter\Document\Sections\Relationship;
use Modules\AbRouter\Models\Events\Event;

/**
 * @property Event $activeData
 */
class EventSchema extends DocumentSchema
{
    public function getIdentifier(): Identifier
    {
        return new Identifier(
            $this->activeData->getEntityId(),
            $this->activeData::getType()
        );
    }

    public function getAttributes(): Attributes
    {
        return new Attributes([
            'user_id' => $this->activeData->user_id,
            'event' => $this->activeData->event,
            'value' => $this->activeData->value,
            'tag' => $this->activeData->tag,
            'referrer' => $this->activeData->referrer,
            'ip' => $this->activeData->ip,
            'meta' => $this->activeData->meta,
            'created_at' => $this->activeData->created_at
        ]);
    }

    public function getRelationships(): RelationshipsCollection
    {
        return new RelationshipsCollection(
            new Relationship(
                'owner',
                new Identifier(
                    $this->activeData->owner->getEntityId(),
                    'users'
                )
            )
        );
    }
}