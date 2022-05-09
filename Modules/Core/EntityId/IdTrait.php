<?php
declare(strict_types=1);

namespace Modules\Core\EntityId;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @mixin Model
 */
trait IdTrait
{
    public function getEntityId(): string
    {
        return (new EntityEncoder())->encode($this->id, $this->getTable());
    }
}
