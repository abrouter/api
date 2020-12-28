<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Http\Resources\ProxyBinding;

use JsonApi\JsonApi\Base\BaseObject;
use JsonApi\JsonApi\Elements\AttributesObject;
use JsonApi\JsonApi\Elements\Relationship;
use JsonApi\JsonApi\Elements\RelationshipsCollection;
use JsonApi\JsonApi\Elements\ResourceIdentifier;
use JsonApi\JsonApi\Elements\ResourceLinkage;
use JsonApi\JsonApi\Elements\ResourceObject;
use Modules\ProxiedMail\Models\ProxyBinding;

/**
 * Class ProxyBindingObject
 * @package Modules\ProxiedMail\Http\Resources\ProxyBinding
 * @property ProxyBinding $model
 */
class ProxyBindingObject extends BaseObject
{
    public function getInstance(): ResourceObject
    {
        $resource = new ResourceObject(
            (string) $this->model->getEntityId(),
            $this->model::getType()
        );

        $attributes = new AttributesObject([
            'real_addresses' => $this->model->realAddresses->pluck('real_address')->toArray(),
            'proxy_address' => $this->model->proxy_address,
            'received_emails' => $this->model->received_emails,
            'created_at' => $this->model->created_at->toDateTimeString(),
            'updated_at' => $this->model->updated_at->toDateTimeString(),
        ]);
        $relationships = new RelationshipsCollection([
            'user' => new Relationship(new ResourceLinkage(new ResourceIdentifier(
                $this->model->user->getEntityId(),
                $this->model->user::getType()
            )))
        ]);

        return $resource
            ->withAttributes($attributes)
            ->withRelationships($relationships);
    }
}
