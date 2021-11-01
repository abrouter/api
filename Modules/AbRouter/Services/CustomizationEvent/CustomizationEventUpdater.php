<?php
declare(strict_types =1);

namespace Modules\AbRouter\Services\CustomizationEvent;

use Modules\AbRouter\Models\CustomizationEvent\DisplayUserEvent;
use Modules\AbRouter\Services\CustomizationEvent\DTO\CustomizationEventUpdateDTO;
use Modules\AbRouter\Http\Middleware\CheckUserMiddleware;

class CustomizationEventUpdater
{
    public function update(CustomizationEventUpdateDTO $customizationEventUpdateDTO)
    {
        $verificationUser = app()->make(CheckUserMiddleware::class);
        $verificationUser->handle($customizationEventUpdateDTO->getUserId());
        $displayUserEvent = new DisplayUserEvent();
        $displayUserEvent->where('id', $customizationEventUpdateDTO->getId())
                        ->update(['user_id' => $customizationEventUpdateDTO->getUserId(),
                                  'event_name' => $customizationEventUpdateDTO->getEventName()
                                ]);
        
        $displayUserEvent = new DisplayUserEvent();
        $updatedEvent = $displayUserEvent->where('id', $customizationEventUpdateDTO->getId())->get();
                                
        return $updatedEvent;
    }
}
