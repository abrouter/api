<?php
declare(strict_types = 1);

namespace Modules\AbRouter\Http\Resources2\CustomizationEvent;

use AbRouter\JsonApiFormatter\Document\Collections\RelationshipsCollection;
use AbRouter\JsonApiFormatter\Document\Schema\DocumentSchema;
use AbRouter\JsonApiFormatter\Document\Sections\Attributes;
use AbRouter\JsonApiFormatter\Document\Sections\Identifier;
use AbRouter\JsonApiFormatter\Document\Sections\Relationship;
use Modules\AbRouter\Models\CustomizationEvent\DisplayUserEvent;

/**
 * @property  DisplayUserEvent activeData
 */
class CustomizationEventSchema extends DocumentSchema
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
            'event_name' => $this->activeData->event_name,
        ]);
    }

    public function getRelationships(): RelationshipsCollection
    {
        return new RelationshipsCollection(
            new Relationship(
                'user_id',
                new Identifier(
                    $this->activeData->user->getEntityId(),
                    $this->activeData->user::getType()
                )
            )
        );
    }
}
