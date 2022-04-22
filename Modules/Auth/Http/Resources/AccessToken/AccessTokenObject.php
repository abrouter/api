<?php
declare(strict_types=1);

namespace Modules\Auth\Http\Resources\AccessToken;

use JsonApi\JsonApi\Base\BaseObject;
use JsonApi\JsonApi\Elements\AttributesObject;
use JsonApi\JsonApi\Elements\Relationship;
use JsonApi\JsonApi\Elements\RelationshipsCollection;
use JsonApi\JsonApi\Elements\ResourceIdentifier;
use JsonApi\JsonApi\Elements\ResourceLinkage;
use JsonApi\JsonApi\Elements\ResourceObject;
use Modules\Auth\Entities\User\UserWithAccessToken;

/**
 * Class UserObject
 * @package Modules\Auth\Http\Resources\User
 * @property UserWithAccessToken $model
 */
class AccessTokenObject extends BaseObject
{
    public function getInstance(): ResourceObject
    {
        $resource = new ResourceObject(
            (string) $this->model->getEntityId(),
            $this->model::getType()
        );

        $attributes = new AttributesObject([
            'token' => $this->model->getAccessToken()->getToken(),
            'expires_at' => $this->model->getAccessToken()->expiresAt(),
        ]);
        $relationships = new RelationshipsCollection([
            'user' => new Relationship(new ResourceLinkage(new ResourceIdentifier(
                $this->model->getUser()->getEntityId(),
                'users'
            )))
        ]);

        return $resource
            ->withAttributes($attributes)
            ->withRelationships($relationships);
    }
}
