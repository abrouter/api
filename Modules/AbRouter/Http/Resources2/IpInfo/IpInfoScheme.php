<?php
declare(strict_types = 1);

namespace Modules\AbRouter\Http\Resources2\IpInfo;

use AbRouter\JsonApiFormatter\Document\Schema\DocumentSchema;
use AbRouter\JsonApiFormatter\Document\Sections\Attributes;
use AbRouter\JsonApiFormatter\Document\Sections\Identifier;
use Modules\AbRouter\Entities\IpInfoEntity;

/**
 * @property  IpInfoEntity activeData
 */
class IpInfoScheme extends DocumentSchema
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
            'ip' => $this->activeData->getIp(),
            'city' => $this->activeData->getCity(),
            'countryCode' => $this->activeData->getCountryCode(),
            'countryName' => $this->activeData->getCountryName(),
        ]);
    }
}