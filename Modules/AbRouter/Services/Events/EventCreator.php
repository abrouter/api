<?php
declare(strict_types =1);

namespace Modules\AbRouter\Services\Events;

use Modules\AbRouter\Models\Events\Event;
use Modules\AbRouter\Services\Events\DTO\EventDTO;
use Modules\AbRouter\Services\RelatedUser\DTO\RelatedUserDTO;
use Modules\AbRouter\Services\RelatedUser\RelatedUserCreator;

class EventCreator
{
    /**
     * @var RelatedUserCreator
     */
    private $relatedUserCreator;
    
    public function __construct(RelatedUserCreator $relatedUserCreator)
    {
        $this->relatedUserCreator = $relatedUserCreator;
    }
    
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
            'created_at' => $eventDTO->getCreatedAt() ?? (new \DateTime())->format('Y-m-d'),
            'country_code' => $eventDTO->getCountryCode(),
        ]);

        $saved = $event->save();

        if ($saved) {
            $this->relatedUserCreator->create(new RelatedUserDTO(
                $eventDTO->getOwnerId(),
                $eventDTO->getUserId(),
                $eventDTO->getTemporaryUserId(),
                $event->id
            ));
        }
        
        return $event;
    }
}
