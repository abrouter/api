<?php
declare(strict_types = 1);

namespace Modules\AbRouter\Http\Resources2\ExperimentBranch;

use AbRouter\JsonApiFormatter\Document\Collections\RelationshipsCollection;
use AbRouter\JsonApiFormatter\Document\Schema\DocumentSchema;
use AbRouter\JsonApiFormatter\Document\Sections\Attributes;
use AbRouter\JsonApiFormatter\Document\Sections\Identifier;
use AbRouter\JsonApiFormatter\Document\Sections\Relationship;
use Modules\AbRouter\Models\Experiments\ExperimentBranches;

/**
 * @property ExperimentBranches $activeData
 */
class ExperimentBranchScheme extends DocumentSchema
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
            'name' => $this->activeData->name,
            'uid' => $this->activeData->uid,
            'percent' => $this->activeData->percent,
            'config' => json_decode($this->activeData->config, true),
        ]);
    }

    public function getRelationships(): RelationshipsCollection
    {
        return new RelationshipsCollection(
            new Relationship(
                'experiment',
                new Identifier(
                    $this->activeData->experiment->getEntityId(),
                    'users'
                ),
            ),
        );
    }
}
