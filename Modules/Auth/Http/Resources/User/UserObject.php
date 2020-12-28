<?php
declare(strict_types=1);

namespace Modules\Auth\Http\Resources\User;

use JsonApi\JsonApi\Base\BaseObject;
use JsonApi\JsonApi\Elements\AttributesObject;
use JsonApi\JsonApi\Elements\ResourceObject;
use Modules\Auth\Models\User\User;
use Modules\Core\EntityId\Encoder;

/**
 * Class UserObject
 * @package Modules\Auth\Http\Resources\User
 * @property User $model
 */
class UserObject extends BaseObject
{
    public function getInstance(): ResourceObject
    {
        $resource = new ResourceObject(
            (new Encoder())->encode($this->model->id, $this->model->getTable()),
            $this->model->getTable()
        );

        $attributes = new AttributesObject([
            'username' => $this->model->username,
            'created_at' => $this->model->created_at->toDateTimeString(),
            'updated_at' => $this->model->updated_at->toDateTimeString(),
        ]);

        return $resource
            ->withAttributes($attributes);
    }
}
