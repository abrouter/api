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
     * @return array
     */
    public function getCounters(
        Collection $eventsList,
        array $uniqUsers,
        array $allDisplayEvents
    ): array {
        $eventCounters = [];

        foreach ($eventsList as $event) {
            /**
             * @var Event $event
             */

            if (in_array($event->event, $allDisplayEvents, true) && in_array($event->user_id, $uniqUsers, true)) {
                $convertDate = $event->created_at->format('Y-m-d');

                if (!isset($eventCounters[$event->event][$convertDate])) {
                    $eventCounters[$event->event][$convertDate] = 0;
                }

                $eventCounters[$event->event][$convertDate]++;
            }
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
