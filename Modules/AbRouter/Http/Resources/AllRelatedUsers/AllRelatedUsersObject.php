<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Resources\AllRelatedUsers;

use JsonApi\JsonApi\Base\BaseObject;
use JsonApi\JsonApi\Elements\AttributesObject;
use JsonApi\JsonApi\Elements\ResourceObject;
use Modules\AbRouter\Services\RelatedUser\AllRelatedUsersServices;

/**
 * Class RelatedUserObject
 * @property AllRelatedUsersServices $model
 */
class AllRelatedUsersObject extends BaseObject
{
    public function getInstance(): ResourceObject
    {
        $resource = new ResourceObject(
            '',
            'related_users'
        );

        $attributes = new AttributesObject([
            'related_user_id' => $this->model,
        ]);

        return $resource
            ->withAttributes($attributes);
    }
}
