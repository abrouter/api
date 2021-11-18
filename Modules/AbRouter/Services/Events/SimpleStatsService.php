<?php
declare(strict_types =1);

namespace Modules\AbRouter\Services\Events;

use Illuminate\Support\Collection;
use Modules\AbRouter\Models\CustomizationEvent\DisplayUserEvent;
use Modules\AbRouter\Models\Events\Event;
use Modules\AbRouter\Models\RelatedUsers\RelatedUser;
use Modules\AbRouter\Repositories\Events\EventsRepository;
use Modules\AbRouter\Repositories\Events\UserEventsRepository;
use Modules\AbRouter\Repositories\RelatedUser\RelatedUserRepository;
use Modules\AbRouter\Services\Events\DTO\StatsQueryDTO;
use Modules\AbRouter\Services\Events\DTO\StatsResultsDTO;

class SimpleStatsService
{
    /**
     * @var UserEventsRepository
     */
    private $userEventsRepository;
    
    /**
     * @var EventsRepository
     */
    private $eventsRepository;
    
    /**
     * @var RelatedUserRepository
     */
    private $relatedUserRepository;
    
    public function __construct(
        UserEventsRepository $userEventsRepository,
        EventsRepository $eventsRepository,
        RelatedUserRepository $relatedUserRepository
    ) {
        $this->userEventsRepository = $userEventsRepository;
        $this->eventsRepository = $eventsRepository;
        $this->relatedUserRepository = $relatedUserRepository;
    }
    
    public function getStats(StatsQueryDTO $statsQueryDTO): StatsResultsDTO
    {
        $allUserEvents = $this->userEventsRepository->getWithOwnerByTag(
            $statsQueryDTO->getOwnerId(),
            $statsQueryDTO->getTag()
        );
        $allUserEvents->load('relatedUsers');
        $allRelatedUsers = $allUserEvents->pluck('relatedUsers')->flatten();
        
        $eventsNames = $this->getDisplayEvents($statsQueryDTO->getOwnerId());
        
        $uniqUsersIds = $this->getUniqUsersIds($allUserEvents);
        $uniqRelatedUsersIds = $this->getUniqRelatedUsersIds($uniqUsersIds, $allRelatedUsers->all());
        $uniqUsers  = $this->getFinalUniqUsers($uniqUsersIds, $uniqRelatedUsersIds);
        
        $uniqUsersCount = count($uniqUsers);
        
        $eventCounters = $this->getEventCounters(
            $allUserEvents,
            $uniqUsers
        );
        
        $eventPercentages = $this->getEventsPercentages(
            $eventsNames,
            $eventCounters,
            $uniqUsersCount
        );
        
        return new StatsResultsDTO($eventPercentages, $eventCounters);
    }
    
    private function getUniqUsersIds(Collection $allUserEvents): array
    {
        $users = $allUserEvents->reduce(function (array $acc, Event $event) {
            $acc[] = $event->user_id;
            return $acc;
        }, []);
        $users = array_unique($users);
        
        return $users;
    }
    
    private function getDisplayEvents(int $ownerId): array
    {
        return $this
            ->eventsRepository
            ->getEventsByUser($ownerId)
            ->reduce(function (array $acc, DisplayUserEvent $displayUserEvent) {
                $acc[] = $displayUserEvent->event_name;
                return $acc;
            }, []);
    }
    
    private function getUniqRelatedUsersIds(array $uniqUsersIds, array $allRelatedUsers): array
    {
        $relatedUsersIds = [];
        $glueUserRelatedUser = [];
        $glueRelatedUser = [];
        
        
        foreach ($allRelatedUsers as $relatedUser) {
            /**
             * @var RelatedUser $relatedUser
             */
            if (empty($relatedUser->user_id) || empty($relatedUser->related_user_id)) {
                continue;
            }
            
            $glueUserRelatedUser[$relatedUser->user_id][] = $relatedUser->related_user_id;
            $glueRelatedUser[$relatedUser->related_user_id][] = $relatedUser->user_id;
        }
        
        foreach ($allRelatedUsers as $relatedUser) {
            /**
             * @var RelatedUser $relatedUser
             */
            if (empty($relatedUser->related_user_id)) {
                continue;
            }
            
            if (!empty($glueUserRelatedUser[$relatedUser->user_id])) {
                continue;
            }
    
            if (!empty($glueRelatedUser[$relatedUser->related_user_id])) {
                continue;
            }
    
            $relatedUsersIds[$relatedUser->related_user_id] = $relatedUser->related_user_id;
        }
        
        return array_unique($relatedUsersIds);
    }
    
    private function getFinalUniqUsers(array $usersIds, array $relatedUsersIds): array
    {
        return array_unique(array_merge($usersIds, $relatedUsersIds));
    }
    
    private function getEventsPercentages(array $events, array $eventCounters, int $uniqUsersCount): array
    {
        $eventPercentage = [];
        foreach ($events as $eventName) {
            if (!isset($eventCounters[$eventName])) {
                $eventPercentage[$eventName] = 0;
                continue;
            }
            
            $counter = $eventCounters[$eventName];
        
            if ($uniqUsersCount === 0) {
                $eventPercentage[$eventName] = 0;
                continue;
            }
            $eventPercentage[$eventName] = intval(($counter / $uniqUsersCount) * 100);
        }
        
        return $eventPercentage;
    }
    
    private function getEventCounters(
        Collection $eventsList,
        array $uniqUsers
    ): array {
        
        $uniqUsers = array_filter(array_flip($uniqUsers));
        $eventCounters = [];
        $userEventAdded = [];
        
        foreach ($eventsList as $event) {
            /**
             * @var Event $event
             */
                
            $relatedUsersIds = $event
                ->relatedUsers
                ->reduce(function (array $acc, RelatedUser $relatedUser) {
                    if (empty($relatedUser->related_user_id)) {
                        return $acc;
                    }
                    
                    $acc[] = $relatedUser->related_user_id;
                    return $acc;
                }, []);
    
            $userEventKey = $event->event . '_' . $event->user_id;
            
            if (!empty($event->user_id) && isset($userEventAdded[$userEventKey])) {
                continue;
            }
                
            sort($relatedUsersIds);
            if (!empty($relatedUsersIds)) {
                $relatedUsersKey = $event->event . '_' . join('_', $relatedUsersIds);
            } else {
                $relatedUsersKey = '';
            }
                
            if (!empty($relatedUsersKey) && isset($userEventAdded[$relatedUsersKey])) {
                continue;
            }
                
            $hasUserInRelated = false;
            foreach ($relatedUsersIds as $relatedUsersId) {
                if (isset($uniqUsers[$relatedUsersId])) {
                    $hasUserInRelated = true;
                    break;
                }
            }
                
            if (!$hasUserInRelated && !isset($uniqUsers[$event->user_id])) {
                continue;
            }
                
            if ($hasUserInRelated) {
                $userEventAdded[$relatedUsersKey] = true;
            }
            if (isset($uniqUsers[$event->user_id])) {
                $userEventAdded[$userEventKey] = true;
            }
    
            if (!isset($eventCounters[$event->event])) {
                $eventCounters[$event->event] = 0;
            }
                
            $eventCounters[$event->event] ++;
        }
        
        return $eventCounters;
    }
}
