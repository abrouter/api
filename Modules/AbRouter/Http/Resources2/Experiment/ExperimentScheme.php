<?php
declare(strict_types = 1);

namespace Modules\AbRouter\Http\Resources2\Experiment;

use AbRouter\JsonApiFormatter\DataSource\DataProviders\SimpleDataProvider;
use AbRouter\JsonApiFormatter\Document\Collections\IdentifiersCollection;
use AbRouter\JsonApiFormatter\Document\Collections\IncludeSectionsCollection;
use AbRouter\JsonApiFormatter\Document\Collections\RelationshipsCollection;
use AbRouter\JsonApiFormatter\Document\Schema\DocumentSchema;
use AbRouter\JsonApiFormatter\Document\Sections\Attributes;
use AbRouter\JsonApiFormatter\Document\Sections\Identifier;
use AbRouter\JsonApiFormatter\Document\Sections\IncludeSection;
use AbRouter\JsonApiFormatter\Document\Sections\Meta;
use AbRouter\JsonApiFormatter\Document\Sections\Relationship;
use Modules\AbRouter\Http\Resources2\ExperimentBranch\ExperimentBranchScheme;
use Modules\AbRouter\Models\Experiments\Experiment;
use Modules\AbRouter\Models\Experiments\ExperimentBranches;

/**
 * @property Experiment $activeData
 */
class ExperimentScheme extends DocumentSchema
{

    private array $meta = [];

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
            'alias' => $this->activeData->alias,
            'config' => json_decode($this->activeData->config, true),
            'is_enabled' => $this->activeData->is_enabled,
            'is_feature_toggle' => $this->activeData->is_feature_toggle
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
            ),
            new Relationship(
                'branches',
                new IdentifiersCollection(
                    ...$this
                        ->activeData
                        ->branches
                        ->reduce(function (array $acc, ExperimentBranches $branch) {
                            $acc[] = new Identifier($branch->getEntityId(), $branch::getType());
                            return $acc;
                        }, [])
                )
            )
        );
    }

    public function getIncludes(): IncludeSectionsCollection
    {
        return new IncludeSectionsCollection(
          new IncludeSection('branches', function () {
              return new ExperimentBranchScheme(
                  new SimpleDataProvider($this->activeData->branches)
              );
          })
        );
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

    public function getAllowedSearchFields(): array
    {
        return [
            'alias',
        ];
    }
}
