<?php
declare(strict_types=1);

namespace Modules\Core\EntityId;

interface ResourceIdInterface
{
    public function getEntityId(): string;
    public static function getType(): string;
}
