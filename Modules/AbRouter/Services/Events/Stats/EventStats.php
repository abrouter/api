<?php

namespace Modules\AbRouter\Services\Events\Stats;

use Illuminate\Support\Collection;
use Modules\AbRouter\Models\Events\Event;
use Modules\AbRouter\Models\RelatedUsers\RelatedUser;
use Modules\Core\Interfaces\Stats\Stats;

class EventStats implements Stats
{
    /**
     * @param Collection $eventsList
     * @param array $uniqUsers
     * @param array $allDisplayEvents
     * @param bool $enableDateCounter
     * @return array
     */
    public function getCounters(
        Collection $eventsList,
        array $uniqUsers,
        array $allDisplayEvents,
        bool $enableDateCounter = false
    ): array {
        $uniqUsers = array_flip($uniqUsers);
        $eventCounters = [];
        $userEventAdded = [];

        foreach ($eventsList as $event) {
            /**
             * @var Event $event
             */

            if (!in_array($event->event, $allDisplayEvents)) {
                continue;
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

            if ($enableDateCounter) {
                $convertDate = $event->created_at->format('Y-m-d');

                if (!isset($eventCounters[$event->event][$convertDate])) {
                    $eventCounters[$event->event][$convertDate] = 0;
                }

                $eventCounters[$event->event][$convertDate] ++;

                continue;
            }

            if (!isset($eventCounters[$event->event])) {
                $eventCounters[$event->event] = 0;
            }

            $eventCounters[$event->event] ++;
        }

        return $eventCounters;
    }

    /**
     * @param array $allDisplayEvents
     * @param array $eventCounters
     * @param int $uniqUsersCount
     * @return array
     */
    public function getPercentages(
        array $allDisplayEvents,
        array $eventCounters,
        int $uniqUsersCount
    ): array {
        $eventPercentage = [];
        foreach ($allDisplayEvents as $displayEvent) {
            if ($displayEvent['type'] === 'summarizable') {
                continue;
            }

            if (!isset($eventCounters[$displayEvent['event_name']])) {
                continue;
            }

            $counter = $eventCounters[$displayEvent['event_name']];

            if ($uniqUsersCount === 0) {
                $eventPercentage[$displayEvent['event_name']] = 0;
                continue;
            }
            $eventPercentage[$displayEvent['event_name']] = intval(($counter / $uniqUsersCount) * 100);
        }

        return $eventPercentage;
    }
}
