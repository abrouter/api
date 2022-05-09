<?php
declare(strict_types = 1);

namespace Modules\AbRouter\Http\Resources2\Tag;

use AbRouter\JsonApiFormatter\Document\Schema\DocumentSchema;
use AbRouter\JsonApiFormatter\Document\Sections\Attributes;
use AbRouter\JsonApiFormatter\Document\Sections\Identifier;
use Modules\AbRouter\Models\Events\Event;
use Modules\Auth\Exposable\AuthDecorator;

/**
 * @property Event $activeData
 */
class TagScheme extends DocumentSchema
{
    public function getIdentifier(): Identifier
    {
        $authDecorator = app()->make(AuthDecorator::class);

        return new Identifier(
            (string) $authDecorator->get()->getEntityId(),
            'tags'
        );
    }

    public function getAttributes(): Attributes
    {
        return new Attributes([
            'tag' => $this->activeData->tag,
        ]);
    }
}
