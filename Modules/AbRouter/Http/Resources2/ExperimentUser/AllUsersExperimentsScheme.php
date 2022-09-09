<?php
declare(strict_types = 1);

namespace Modules\AbRouter\Http\Resources2\ExperimentUser;

use AbRouter\JsonApiFormatter\Document\Schema\DocumentSchema;
use AbRouter\JsonApiFormatter\Document\Sections\Attributes;
use AbRouter\JsonApiFormatter\Document\Sections\Identifier;
use Modules\AbRouter\Models\Experiments\ExperimentUsers;
use Modules\Auth\Exposable\AuthDecorator;

/**
 * @property ExperimentUsers $activeData
 */
class AllUsersExperimentsScheme extends DocumentSchema
{
    public function getIdentifier(): Identifier
    {
        $authDecorator = app()->make(AuthDecorator::class);

        return new Identifier(
            (string) $authDecorator->get()->getEntityId(),
            'users_experiments'
        );
    }

    public function getAttributes(): Attributes
    {
        return new Attributes([
            $this->activeData
        ]);
    }
}

