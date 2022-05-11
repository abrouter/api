<?php
declare(strict_types = 1);

namespace Modules\Auth\Http\Resources2;

use AbRouter\JsonApiFormatter\Document\Schema\DocumentSchema;
use AbRouter\JsonApiFormatter\Document\Sections\Attributes;
use AbRouter\JsonApiFormatter\Document\Sections\Identifier;
use AbRouter\JsonApiFormatter\Document\Sections\Meta;
use Modules\Auth\Models\User\User;
use Modules\Core\EntityId\EntityEncoder;

/**
 *  @property User $activeData
 */
class UserScheme extends DocumentSchema
{
    private $meta = [];

    public function getIdentifier(): Identifier
    {
        return new Identifier(
            (new EntityEncoder())->encode($this->activeData->id, $this->activeData->getTable()),
            $this->activeData->getTable()
        );
    }

    public function getAttributes(): Attributes
    {
        return new Attributes([
            'username' => $this->activeData->username,
            'created_at' => $this->activeData->created_at->toDateTimeString(),
            'updated_at' => $this->activeData->updated_at->toDateTimeString(),
        ]);
    }

    public function addMeta(array $meta): self
    {
        $this->meta = $meta;
        return $this;
    }

    public function getMeta(): Meta
    {
        return new Meta($this->meta);
    }
}