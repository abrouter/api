<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Resources\Tag;

use JsonApi\JsonApi\Base\BaseObject;
use JsonApi\JsonApi\Elements\AttributesObject;
use JsonApi\JsonApi\Elements\Relationship;
use JsonApi\JsonApi\Elements\RelationshipsCollection;
use JsonApi\JsonApi\Elements\ResourceIdentifier;
use JsonApi\JsonApi\Elements\ResourceIdentifierCollection;
use JsonApi\JsonApi\Elements\ResourceLinkage;
use JsonApi\JsonApi\Elements\ResourceObject;
use Modules\AbRouter\Models\Events\Event;
use Modules\Auth\Exposable\AuthDecorator;

/**
 * Class UserObject
 * @package Modules\Auth\Http\Resources\User
 * @property Event $model
 */
class TagObject extends BaseObject
{
    public function getInstance(): ResourceObject
    {
        $authDecorator = app()->make(AuthDecorator::class);

        $resource = new ResourceObject(
            (string) $authDecorator->get()->getEntityId(),
            'tags'
        );

        $attributes = new AttributesObject([
            'tag' => $this->model->tag,
        ]);

        return $resource
            ->withAttributes($attributes);
    }
}
