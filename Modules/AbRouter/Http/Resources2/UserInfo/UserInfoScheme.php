<?php
declare(strict_types = 1);

namespace Modules\AbRouter\Http\Resources2\UserInfo;

use AbRouter\JsonApiFormatter\Document\Schema\DocumentSchema;
use AbRouter\JsonApiFormatter\Document\Sections\Attributes;
use AbRouter\JsonApiFormatter\Document\Sections\Identifier;
use Modules\AbRouter\Entities\UserInfoEntity;
use Modules\Auth\Exposable\AuthDecorator;

/**
 * @property  UserInfoEntity activeData
 */
class UserInfoScheme extends DocumentSchema
{
    public function getIdentifier(): Identifier
    {
        $authDecorator = app()->make(AuthDecorator::class);

        return new Identifier(
            $authDecorator->get()->getEntityId(),
            'user_info'
        );
    }

    public function getAttributes(): Attributes
    {
        return new Attributes([
            'experiments_ids' => $this->activeData->getExperimentsIds(),
            'created_at' => $this->activeData->getCreatedAt(),
            'browser' => $this->activeData->getBrowser(),
            'platform' => $this->activeData->getPlatform(),
            'country_name' => $this->activeData->getCountryName()
        ]);
    }
}