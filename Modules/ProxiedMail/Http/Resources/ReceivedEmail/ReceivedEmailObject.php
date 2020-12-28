<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Http\Resources\ReceivedEmail;

use JsonApi\JsonApi\Base\BaseObject;
use JsonApi\JsonApi\Elements\AttributesObject;
use JsonApi\JsonApi\Elements\ResourceObject;
use Modules\ProxiedMail\Models\ReceivedEmail;

/**
 * Class ProxyBindingObject
 * @package Modules\ProxiedMail\Http\Resources\ProxyBinding
 * @property ReceivedEmail $model
 */
class ReceivedEmailObject extends BaseObject
{
    public function getInstance(): ResourceObject
    {
        $resource = new ResourceObject(
            (string) $this->model->getEntityId(),
            $this->model::getType()
        );

        $attributes = new AttributesObject([
            'recipient_email' => $this->model->recipient_email,
            'is_processed' => $this->model->is_processed,
            'created_at' => $this->model->created_at->toDateTimeString(),
            'updated_at' => $this->model->updated_at->toDateTimeString(),
        ]);

        return $resource
            ->withAttributes($attributes);
    }
}
