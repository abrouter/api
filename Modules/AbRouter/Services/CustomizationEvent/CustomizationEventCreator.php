<?php
declare(strict_types =1);

namespace Modules\AbRouter\Services\CustomizationEvent;

use Modules\AbRouter\Models\CustomizationEvent\DisplayUserEvent;
use Modules\AbRouter\Services\CustomizationEvent\DTO\CustomizationEventDTO;

class CustomizationEventCreator
{
    public function create(CustomizationEventDTO $customizationEventDTO): DisplayUserEvent
    {
        $displayUserEvent = new DisplayUserEvent();
        $displayUserEvent->fill([
            'user_id' => $customizationEventDTO->getUserId(),
            'event_name' => $customizationEventDTO->getEventName(),
            'order' => $customizationEventDTO->getOrder()
        ]);
        $displayUserEvent->save();
        
        return $displayUserEvent;
    }
}
