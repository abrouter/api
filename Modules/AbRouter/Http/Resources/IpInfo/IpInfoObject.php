<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Resources\IpInfo;

use JsonApi\JsonApi\Base\BaseObject;
use JsonApi\JsonApi\Elements\AttributesObject;
use JsonApi\JsonApi\Elements\Relationship;
use JsonApi\JsonApi\Elements\RelationshipsCollection;
use JsonApi\JsonApi\Elements\ResourceIdentifier;
use JsonApi\JsonApi\Elements\ResourceLinkage;
use JsonApi\JsonApi\Elements\ResourceObject;
use Modules\AbRouter\Entities\IpInfoEntity;

/**
 * @property  IpInfoEntity model
 */
class IpInfoObject extends BaseObject
{
    public function getInstance(): ResourceObject
    {
        $resource = new ResourceObject(
            $this->model->getEntityId(),
            $this->model::getType()
        );

        $attributes = new AttributesObject([
            'ip' => $this->model->getIp(),
            'city' => $this->model->getCity(),
            'countryCode' => $this->model->getCountryCode(),
            'countryName' => $this->model->getCountryName(),
        ]);


        return $resource->withAttributes($attributes);
    }
}
