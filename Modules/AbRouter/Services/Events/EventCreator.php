<?php
declare(strict_types =1);

namespace Modules\AbRouter\Services\Events;

use Modules\AbRouter\Models\Events\Event;
use Modules\AbRouter\Services\Events\DTO\EventDTO;

class EventCreator
{
    public function create(EventDTO $eventDTO): Event
    {
        $event = new Event();
        $event->fill([
            'owner_id' => $eventDTO->getOwnerId(),
            'temporary_user_id' => $eventDTO->getTemporaryUserId(),
            'user_id' => $eventDTO->getUserId(),
            'event' => $eventDTO->getEvent(),
            'ip' => $eventDTO->getIp(),
            'tag' => $eventDTO->getTag(),
            'referrer' => $eventDTO->getReferrer(),
            'meta' => json_encode($eventDTO->getMeta()),
            'country_code' => $eventDTO->getCountryCode(),
        ]);
        
        $event->save();
        return $event;
    }
}
