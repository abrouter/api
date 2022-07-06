<?php
declare(strict_types = 1);

namespace Modules\AbRouter\Http\Resources2\ExperimentBranchUser;

use AbRouter\JsonApiFormatter\DataSource\DataProviders\SimpleDataProvider;
use AbRouter\JsonApiFormatter\Document\Collections\IncludeSectionsCollection;
use AbRouter\JsonApiFormatter\Document\Collections\RelationshipsCollection;
use AbRouter\JsonApiFormatter\Document\Schema\DocumentSchema;
use AbRouter\JsonApiFormatter\Document\Sections\Attributes;
use AbRouter\JsonApiFormatter\Document\Sections\Identifier;
use AbRouter\JsonApiFormatter\Document\Sections\IncludeSection;
use AbRouter\JsonApiFormatter\Document\Sections\Relationship;
use Modules\AbRouter\Http\Resources2\ExperimentBranch\ExperimentBranchScheme;
use Modules\AbRouter\Models\Experiments\ExperimentBranchUser;

/**
 * @property ExperimentBranchUser $activeData
 */
class ExperimentBranchUserScheme extends DocumentSchema
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
        $uid = sprintf('%s-%s', $this->activeData->experiment->uid, $this->activeData->experimentBranch->uid);
        return new Attributes([
            'run-uid' => $uid,
            'branch-uid' => $this->activeData->experimentBranch->uid,
            'experiment-uid' => $this->activeData->experiment->alias,
            'created_at' => $this->activeData->created_at,
        ]);
    }

    public function getRelationships(): RelationshipsCollection
    {
        return new RelationshipsCollection(
            new Relationship(
                'experiment_user',
                new Identifier(
                    $this->activeData->experimentUser->getEntityId(),
                    'experiment_user'
                )
            ),
            new Relationship(
                'experiment_id',
                new Identifier(
                    $this->activeData->experiment->getEntityId(),
                    'users'
                )
            ),
            new Relationship(
                'experiment_branch_id',
                new Identifier(
                    $this->activeData->experimentBranch->getEntityId(),
                    'users'
                )
            )
        );
    }

    public function getIncludes(): IncludeSectionsCollection
    {
        return new IncludeSectionsCollection(
          new IncludeSection(
              'experiment_branch_user',
              function () {
                  return new ExperimentBranchScheme(new SimpleDataProvider($this->activeData->experimentBranch));
              }
          )
        );
    }
}
