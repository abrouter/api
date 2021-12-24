<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Resources\FeatureToggle;

use JsonApi\JsonApi\Elements\AttributesObject;
use JsonApi\JsonApi\Elements\JsonApi;
use JsonApi\JsonApi\Elements\Relationship;
use JsonApi\JsonApi\Elements\RelationshipsCollection;
use JsonApi\JsonApi\Elements\ResourceIdentifier;
use JsonApi\JsonApi\Elements\ResourceLinkage;
use JsonApi\JsonApi\Elements\ResourceObject;
use JsonApi\JsonApi\Elements\ResourceObjectCollection;
use JsonApi\JsonApi\Factories\ObjectFactory;
use JsonApi\JsonApi\Responses\JsonResource;
use Modules\AbRouter\Http\Resources\ExperimentBranch\ExperimentBranchObject;
use Modules\AbRouter\Models\Experiments\ExperimentBranchUser;
use Modules\Auth\Entities\AccessToken\UserWithAccessToken;

/**
 * Class AccessTokenResource
 * @package Modules\Auth\Http\Resources\AccessToken
 * @property UserWithAccessToken
 * @property ExperimentBranchObject $resource
 */
class FeatureToggleResource extends JsonResource
{
    public function jsonApiRoot(): JsonApi
    {
        $obj = new ExperimentBranchObject($this->resource->experimentBranch);
        $objArr = $obj->getInstance()->toArray();
        $experimentRelationship = $objArr['relationships']['experiment']['data'];
        $relationships = ['experiment' => new Relationship(
            new ResourceLinkage(new ResourceIdentifier($experimentRelationship['id'], $experimentRelationship['type']))
        )];

        $branchId = $this->resource->experimentBranch->getEntityId();
        $type = $this->resource->experimentBranch::getType();

        $resourceObject = (new ResourceObject($branchId, $type))
            ->withAttributes(new AttributesObject($objArr['attributes']))
            ->withRelationships(new RelationshipsCollection($relationships));

        return (new JsonApi(
            (new ObjectFactory())->get(FeatureToggleObject::class, $this->resource)
        ))->addIncluded(new ResourceObjectCollection([
            $resourceObject
        ]));
    }
}
