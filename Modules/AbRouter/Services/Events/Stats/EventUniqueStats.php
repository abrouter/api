<?php

namespace Modules\AbRouter\Services\Events\Stats;

use Illuminate\Support\Collection;
use Modules\AbRouter\Models\Events\Event;
use Modules\AbRouter\Models\RelatedUsers\RelatedUser;
use Modules\Core\Interfaces\Stats\Stats;

class EventUniqueStats implements Stats
{
    /**
     * @param Collection $eventsList
     * @param array $uniqUsers
     * @param array $allDisplayEvents
     * @return array
     */
    public function getCounters(
        Collection $eventsList,
        array $uniqUsers,
        array $allDisplayEvents
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

            if (!$hasUserInRelated && !isset($uniqUsers[$event->user_id]) && !empty($uniqUsers)) {
                continue;
            }

            if ($hasUserInRelated) {
                $userEventAdded[$relatedUsersKey] = true;
            }
            if (isset($uniqUsers[$event->user_id])) {
                $userEventAdded[$userEventKey] = true;
            }

            $convertDate = $event->created_at->format('Y-m-d');

            if (!isset($eventCounters[$event->event][$convertDate])) {
                $eventCounters[$event->event][$convertDate] = 0;
            }

            $eventCounters[$event->event][$convertDate] ++;
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

            $counters = 0;

            foreach ($eventCounters[$displayEvent['event_name']] as $date => $eventCounter) {
                $counter = $eventCounters[$displayEvent['event_name']][$date];

                if ($uniqUsersCount === 0) {
                    $eventPercentage[$displayEvent['event_name']] = 0;
                    continue;
                }

                $counters += $counter;
            }

            $eventPercentage[$displayEvent['event_name']] = $uniqUsersCount > 0 ? intval(($counters / $uniqUsersCount) * 100) : 0;
            $eventPercentage[$displayEvent['event_name']] = $eventPercentage[$displayEvent['event_name']] > 100 ? 100
                : $eventPercentage[$displayEvent['event_name']]; //todo ABR-1301
        }

        return $eventPercentage;
    }
}
