<?php
declare(strict_types =1);

namespace Modules\AbRouter\Services\Events;

use Exception;
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
    protected $userEventsRepository;
    
    /**
     * @var EventsRepository
     */
    protected $eventsRepository;
    
    /**
     * @var RelatedUserRepository
     */
    protected $relatedUserRepository;
    
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
        $date = $this->convertDateTime(
            $statsQueryDTO->getDateFrom(),
            $statsQueryDTO->getDateTo()
        );

        $allUserEvents = $this
            ->userEventsRepository
            ->getWithOwnerByTagAndDate(
                $statsQueryDTO->getOwnerId(),
                $statsQueryDTO->getTag(),
                $date['date_from'],
                $date['date_to']
            )
            ->load('relatedUsers');

        $allRelatedUsers = $allUserEvents->pluck('relatedUsers')->flatten();
        
        $eventsNames = $this->getDisplayEvents($statsQueryDTO->getOwnerId());
        $referrers = $this->getReferrers($statsQueryDTO->getOwnerId());
        
        $uniqUsersIds = $this->getUniqUsersIds($allUserEvents);
        $uniqRelatedUsersIds = $this->getUniqRelatedUsersIds($uniqUsersIds, $allRelatedUsers->all());
        $uniqUsers = $this->getFinalUniqUsers($uniqUsersIds, $uniqRelatedUsersIds);
        
        $uniqUsersCount = count($uniqUsers);
        
        $eventCounters = $this->getCounters(
            $allUserEvents,
            $uniqUsers,
            'event'
        );

        $eventCountersWithDate = $this->getCounters(
            $allUserEvents,
            $uniqUsers,
            'event',
            true
        );
        
        $eventPercentages = $this->getPercentages(
            $eventsNames,
            $eventCounters,
            $uniqUsersCount
        );

        $referrerCounters = $this->getCounters(
            $allUserEvents,
            $uniqUsers,
            'referrer'
        );

        $referrerPercentage = $this->getPercentages(
            $referrers,
            $referrerCounters,
            $uniqUsersCount
        );

        arsort($eventPercentages);
        arsort($referrerCounters);

        return new StatsResultsDTO(
            $eventPercentages,
            $eventCounters,
            $referrerCounters,
            $referrerPercentage,
            $eventCountersWithDate
        );
    }
    
    protected function getUniqUsersIds(Collection $allUserEvents): array
    {
        $users = $allUserEvents->reduce(function (array $acc, Event $event) {
            $acc[] = $event->user_id;
            return $acc;
        }, []);

        $users = array_unique($users);
        $usersFlip = array_flip($users);
        unset($usersFlip['']);

        return array_flip($usersFlip);
    }
    
    protected function getDisplayEvents(int $ownerId): array
    {
        return $this
            ->eventsRepository
            ->getEventsByUser($ownerId)
            ->reduce(function (array $acc, DisplayUserEvent $displayUserEvent) {
                $acc[] = $displayUserEvent->event_name;
                return $acc;
            }, []);
    }

    protected function getReferrers(int $ownerId): array
    {
        return $this
            ->userEventsRepository
            ->getReferrersByOwner($ownerId)
            ->reduce(function (array $acc, Event $event) {
                $check = preg_match(
                    '/((http|https):\/\/([\w.]+\/?))|()/',
                    $event->referrer,
                    $matches
                );

                if ($matches) {
                    $acc[] = $matches[0] === '' ? 'direct' : $matches[0];
                }

                return $acc;
            }, []);
    }

    protected function getUniqRelatedUsersIds(array $uniqUsersIds, array $allRelatedUsers): array
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
    
    protected function getFinalUniqUsers(array $usersIds, array $relatedUsersIds): array
    {
        return array_unique(array_merge($usersIds, $relatedUsersIds));
    }
    
    protected function getPercentages(array $events, array $eventCounters, int $uniqUsersCount): array
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

    protected function getCounters(
        Collection $eventsList,
        array $uniqUsers,
        string $action,
        bool $date = false
    ): array {
        
        $uniqUsers = array_flip($uniqUsers);
        $eventCounters = [];
        $userEventAdded = [];
        
        foreach ($eventsList as $event) {
            /**
             * @var Event $event
             */

            $eventOrReferrer = $action === 'event' ? $event->event : $event->referrer;

            if ($action === 'referrer') {
                preg_match(
                    '/((http|https):\/\/([\w.]+\/?))|()/',
                    $eventOrReferrer,
                    $matches
                );

                if (!$matches) {
                    continue;
                }
                $eventOrReferrer = $matches[0] === '' ? 'direct' : $matches[0];
            }

            $relatedUsersIds = $event
                ->relatedUsers
                ->reduce(function (array $acc, RelatedUser $relatedUser) {
                    if (empty($relatedUser->related_user_id)) {
                        return $acc;
                    }
                    
                    $acc[] = $relatedUser->related_user_id;
                    return $acc;
                }, []);
    
            $userEventKey = $eventOrReferrer . '_' . $event->user_id;
            
            if (!empty($event->user_id) && isset($userEventAdded[$userEventKey])) {
                continue;
            }
                
            sort($relatedUsersIds);
            if (!empty($relatedUsersIds)) {
                $relatedUsersKey = $eventOrReferrer . '_' . join('_', $relatedUsersIds);
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

            if ($date) {
                $convertDate = $event->created_at->format('Y-m-d');

                if (!isset($eventCounters[$eventOrReferrer][$convertDate])) {
                    $eventCounters[$eventOrReferrer][$convertDate] = 0;
                }

                $eventCounters[$eventOrReferrer][$convertDate] ++;

                continue;
            }

            if (!isset($eventCounters[$eventOrReferrer])) {
                $eventCounters[$eventOrReferrer] = 0;
            }

            $eventCounters[$eventOrReferrer] ++;
        }
        
        return $eventCounters;
    }

    /**
     * @throws Exception
     */
    protected function convertDateTime($dateFrom = null, $dateTo = null): array
    {
        if(!empty($dateFrom && $dateTo)) {
            $dateFromConverted = \DateTime::createFromFormat('m-d-Y', $dateFrom)->format('Y-m-d');
            $dateToConverted = \DateTime::createFromFormat('m-d-Y', $dateTo)->format('Y-m-d');
            return ['date_from' => $dateFromConverted, 'date_to' => $dateToConverted];
        }

        $dateFrom = (new \DateTime())->format('Y-m-d');
        $dateTo = (new \DateTime($dateFrom))->add(new \DateInterval('P1D'))->format('Y-m-d');

        return ['date_from' => $dateFrom, 'date_to' => $dateTo];
    }
}
