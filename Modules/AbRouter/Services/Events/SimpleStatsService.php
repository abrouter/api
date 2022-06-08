<?php
declare(strict_types =1);

namespace Modules\AbRouter\Services\Events;

use Exception;
use Illuminate\Support\Collection;
use Modules\AbRouter\Models\Events\Event;
use Modules\AbRouter\Models\RelatedUsers\RelatedUser;
use Modules\AbRouter\Repositories\Events\EventsRepository;
use Modules\AbRouter\Repositories\Events\UserEventsRepository;
use Modules\AbRouter\Repositories\RelatedUser\RelatedUserRepository;
use Modules\AbRouter\Services\Events\Stats\StatsFactory;
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

    /**
     * @var StatsFactory
     */
    protected $statsFactory;
    
    public function __construct(
        UserEventsRepository $userEventsRepository,
        EventsRepository $eventsRepository,
        RelatedUserRepository $relatedUserRepository,
        StatsFactory $statsFactory
    ) {
        $this->userEventsRepository = $userEventsRepository;
        $this->eventsRepository = $eventsRepository;
        $this->relatedUserRepository = $relatedUserRepository;
        $this->statsFactory = $statsFactory;
    }

    /**
     * @throws Exception
     */
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
        
        $allDisplayEvents = $this->getDisplayEvents($statsQueryDTO->getOwnerId());
        $displayEventsWithTypeSummarizable = $this->getDisplayEventsWithTypeSummarizable($allDisplayEvents);
        $referrers = $this->getReferrers($statsQueryDTO->getOwnerId(), $displayEventsWithTypeSummarizable);
        $allRevenue = $this->getAllRevenue($allUserEvents);

        $uniqUsersIds = $this->getUniqUsersIds($allUserEvents);
        $uniqRelatedUsersIds = $this->getUniqRelatedUsersIdsWithoutBinding($allRelatedUsers->all());
        $uniqUsers = $this->getFinalUniqUsers($uniqUsersIds, $uniqRelatedUsersIds);
        
        $uniqUsersCount = count($uniqUsers);

        $incrementalCounters = $this
            ->statsFactory
            ->getStatsMethod('event')
            ->getCounters(
                $allUserEvents,
                $uniqUsers,
                $displayEventsWithTypeSummarizable
            );

        $eventCountersWithDate = $this
            ->statsFactory
            ->getStatsMethod('event')
            ->getCounters(
                $allUserEvents,
                $uniqUsers,
                $displayEventsWithTypeSummarizable,
                true
            );

        $summarizationCounters = $this
            ->statsFactory
            ->getStatsMethod('revenue')
            ->getCounters(
                $allUserEvents,
                [],
                $displayEventsWithTypeSummarizable,
                true
            );
        
        $eventPercentages = $this
            ->statsFactory
            ->getStatsMethod('event')
            ->getPercentages(
                $allDisplayEvents,
                $incrementalCounters,
                $uniqUsersCount
            );

        $referrerCounters = $this
            ->statsFactory
            ->getStatsMethod('referrer')
            ->getCounters(
                $allUserEvents,
                $uniqUsers,
                []
            );

        $referrerPercentage = $this
            ->statsFactory
            ->getStatsMethod('referrer')
            ->getPercentages(
                $referrers,
                $referrerCounters,
                $uniqUsersCount
            );

        arsort($eventPercentages);
        arsort($referrerCounters);

        return new StatsResultsDTO(
            $eventPercentages,
            $incrementalCounters,
            $summarizationCounters,
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
            ->toArray();
    }

    protected function getDisplayEventsWithTypeSummarizable(array $displayEvents): array
    {
        return array_reduce($displayEvents,
            function (array $acc, $displayEvent) {
                if ($displayEvent['type'] === 'summarizable') {
                    $acc[] = $displayEvent['event_name'];

                    return $acc;
                }

                return $acc;
            }, []);
    }

    protected function getReferrers(int $ownerId, array $displayEvents): array
    {
        $referrers = $this
            ->userEventsRepository
            ->getReferrersByOwner($ownerId)
            ->reduce(function (array $acc, Event $event) use ($displayEvents) {
                if (in_array($event->event, $displayEvents)) {
                    return $acc;
                }

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

        return array_unique($referrers);
    }

    protected function getUniqRelatedUsersIdsWithoutBinding(array $allRelatedUsers): array
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

    protected function getUniqRelatedUsersIds(RelatedUser...$relatedUsers): array
    {
        return array_reduce($relatedUsers, function (array $acc, RelatedUser $relatedUser) {
            $acc[] = $relatedUser->user_id;
            $acc[] = $relatedUser->related_user_id;

            return $acc;
        }, []);
    }

    protected function getAllRevenue(Collection $allUserEvents) {
        $revenue = $allUserEvents->reduce(function (array $acc, Event $event) {
            $acc[] += $event->value;

            return $acc;
        }, []);

        return array_sum($revenue);
    }
}
