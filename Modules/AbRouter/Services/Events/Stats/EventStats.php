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
     * @param array $eventsName
     * @param bool $date
     * @return array
     */
    public function getCounters(
        Collection $eventsList,
        array $uniqUsers,
        array $eventsName,
        bool $date = false
    ): array {
        $uniqUsers = array_flip($uniqUsers);
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

            if ($date) {
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

            $eventType = !empty($eventsName) ? $eventsName[$event->event] : '';

            if ($eventType === 'summarizable') {
                if (!isset($eventCounters['summarization'][$event->event])) {
                    is_numeric($event->value) ?
                        $eventCounters['summarization'][$event->event] = $event->value
                        : $eventCounters['summarization'][$event->event] = 0;
                }

                $eventCounters['summarization'][$event->event] += is_numeric($event->value) ?
                    $event->value
                    : 0;
            }

            $eventCounters[$event->event] ++;

        }

        return $eventCounters;
    }

    public function getPercentages(array $events, array $eventCounters, int $uniqUsersCount): array
    {
        $eventPercentage = [];
        foreach ($events as $eventName => $type) {
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
}
