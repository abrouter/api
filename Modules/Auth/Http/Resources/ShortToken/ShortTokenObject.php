<?php
declare(strict_types=1);

namespace Modules\Auth\Http\Resources\ShortToken;

use JsonApi\JsonApi\Base\BaseObject;
use JsonApi\JsonApi\Elements\AttributesObject;
use JsonApi\JsonApi\Elements\ResourceObject;
use Laravel\Passport\Token;
use Modules\Core\EntityId\Encoder;

/**
 * Class UserObject
 * @package Modules\Auth\Http\Resources\User
 * @property Token $model
 */
class ShortTokenObject extends BaseObject
{
    public function getInstance(): ResourceObject
    {
        $resource = new ResourceObject(
            (new Encoder())->encode($this->model->user_id, 'users'),
            'oauth_access_tokens'
        );

        $attributes = new AttributesObject([
            'token' => $this->model->id,
            'expires_at' => $this->model->expires_at,
        ]);

        return $resource
            ->withAttributes($attributes);
    }
}
