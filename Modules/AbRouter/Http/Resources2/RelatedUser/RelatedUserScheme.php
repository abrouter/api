<?php
declare(strict_types = 1);

namespace Modules\AbRouter\Http\Resources2\RelatedUser;

use AbRouter\JsonApiFormatter\Document\Collections\RelationshipsCollection;
use AbRouter\JsonApiFormatter\Document\Schema\DocumentSchema;
use AbRouter\JsonApiFormatter\Document\Sections\Attributes;
use AbRouter\JsonApiFormatter\Document\Sections\Identifier;
use AbRouter\JsonApiFormatter\Document\Sections\Relationship;
use Modules\AbRouter\Models\RelatedUsers\RelatedUser;

/**
 * @property RelatedUser $activeData
 */
class RelatedUserScheme extends DocumentSchema
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
            'related_user_id' => $this->activeData->related_user_id,
        ]);
    }

    public function getRelationships(): RelationshipsCollection
    {
        return new RelationshipsCollection(
            new Relationship(
                'owner',
                new Identifier(
                    $this->activeData->owner->getEntityId(),
                    $this->activeData->owner::getType()
                )
            )
        );
    }
}
