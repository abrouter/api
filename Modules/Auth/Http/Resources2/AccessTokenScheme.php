<?php
declare(strict_types = 1);

namespace Modules\Auth\Http\Resources2;

use AbRouter\JsonApiFormatter\Document\Collections\RelationshipsCollection;
use AbRouter\JsonApiFormatter\Document\Schema\DocumentSchema;
use AbRouter\JsonApiFormatter\Document\Sections\Attributes;
use AbRouter\JsonApiFormatter\Document\Sections\Identifier;
use AbRouter\JsonApiFormatter\Document\Sections\Relationship;
use Modules\Auth\Entities\User\UserWithAccessToken;

/**
 *  @property UserWithAccessToken $activeData
 */
class AccessTokenScheme extends DocumentSchema
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
            'token' => $this->activeData->getAccessToken()->getToken(),
            'expires_at' => $this->activeData->getAccessToken()->expiresAt(),
        ]);
    }

    public function getRelationships(): RelationshipsCollection
    {
        return new RelationshipsCollection(
            new Relationship(
                'user',
                new Identifier(
                    $this->activeData->getUser()->getEntityId(),
                    $this->activeData->getUser()::getType()
                )
            )
        );
    }
}
