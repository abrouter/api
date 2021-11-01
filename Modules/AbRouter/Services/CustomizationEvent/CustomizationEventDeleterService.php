<?php
declare(strict_types =1);

namespace Modules\AbRouter\Services\CustomizationEvent;

use Modules\AbRouter\Models\CustomizationEvent\DisplayUserEvent;
use Modules\AbRouter\Services\CustomizationEvent\DTO\CustomizationEventUpdateDTO;
use Modules\AbRouter\Http\Middleware\CheckUserMiddleware;

class CustomizationEventDeleterService
{
    public function delete(CustomizationEventUpdateDTO $customizationEventUpdateDTO)
    {
        $verificationUser = app()->make(CheckUserMiddleware::class);
        $verificationUser->handle($customizationEventUpdateDTO->getUserId());
        $displayUserEvent = new DisplayUserEvent();
        $displayUserEvent->where('id', $customizationEventUpdateDTO->getId())
                        ->where('user_id', $customizationEventUpdateDTO->getUserId())
                        ->delete();
        
        return $displayUserEvent;
    }
}
