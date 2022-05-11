<?php
declare(strict_types =1);

namespace Modules\AbRouter\Services\Events;

use Modules\AbRouter\Models\Events\Event;
use Modules\AbRouter\Repositories\IpInfo\IpInfoWithCacheRepository;
use Modules\AbRouter\Services\Events\DTO\EventDTO;
use Modules\AbRouter\Services\RelatedUser\DTO\RelatedUserDTO;
use Modules\AbRouter\Services\RelatedUser\RelatedUserCreator;

class EventCreator
{
    /**
     * @var RelatedUserCreator
     */
    private $relatedUserCreator;

    /**
     * @var IpInfoWithCacheRepository
     */
    private $ipInfoWithCacheRepository;

    public function __construct(
        RelatedUserCreator $relatedUserCreator,
        IpInfoWithCacheRepository $ipInfoWithCacheRepository
    ) {
        $this->relatedUserCreator = $relatedUserCreator;
        $this->ipInfoWithCacheRepository = $ipInfoWithCacheRepository;
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
            'meta' => json_encode($this->getMetadata($eventDTO)),
            'created_at' => $eventDTO->getCreatedAt() ?? (new \DateTime())->format('Y-m-d'),
            'country_code' => strtoupper($this->getCountryCode($eventDTO)),
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

    private function getCountryCode(EventDTO $eventDTO): string
    {
        $ipInfo = $this->ipInfoWithCacheRepository->get($eventDTO->getIp());
        if (!empty($eventDTO->getCountryCode()) || $ipInfo === null) {
            return $eventDTO->getCountryCode();
        }

        return $ipInfo->getCountryCode();
    }

    private function getMetadata(EventDTO $eventDTO):  array
    {
        $meta = $eventDTO->getMeta();
        $ipInfo = $this->ipInfoWithCacheRepository->get($eventDTO->getIp());
        if (!isset($meta['geo']) && $ipInfo !== null) {
            $meta['city'] = $ipInfo->getCity();
            $meta['country_name'] = $ipInfo->getCountryName();
        }

        return $meta;
    }
}
