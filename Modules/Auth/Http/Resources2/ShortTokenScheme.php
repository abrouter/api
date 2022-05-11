<?php
declare(strict_types = 1);

namespace Modules\Auth\Http\Resources2;

use AbRouter\JsonApiFormatter\Document\Schema\DocumentSchema;
use AbRouter\JsonApiFormatter\Document\Sections\Attributes;
use AbRouter\JsonApiFormatter\Document\Sections\Identifier;
use Modules\Core\EntityId\EntityEncoder;

/**
 *  @property \Laravel\Passport\Token $activeData
 */
class ShortTokenScheme extends DocumentSchema
{
    public function getIdentifier(): Identifier
    {
        return new Identifier(
            (new EntityEncoder())->encode($this->activeData->user_id, 'users'),
            'oauth_access_tokens'
        );
    }

    public function getAttributes(): Attributes
    {
        return new Attributes([
            'token' => $this->activeData->id,
            'expires_at' => $this->activeData->expires_at,
        ]);
    }
}
