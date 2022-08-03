<?php

namespace Modules\AbRouter\Services\Events\Stats;

use Illuminate\Support\Collection;
use Modules\AbRouter\Models\Events\Event;
use Modules\Core\Interfaces\Stats\Stats;

class RevenueStats implements Stats
{
    /**
     * @param Collection $eventsList
     * @param array $uniqUsers
     * @param array $displayEvents
     * @param bool $enableDateCounter
     * @return array
     */
    public function getCounters(
        Collection $eventsList,
        array $uniqUsers,
        array $displayEvents,
        bool $enableDateCounter = false
    ): array {
        $revenueCounters = [];

        foreach ($eventsList as $event) {
            /**
             * @var Event $event
             */

            if (in_array($event->event, $displayEvents, true)) {
                $convertDate = $event->created_at->format('Y-m-d');

                if (is_numeric($event->value)) {
                    if (!isset($revenueCounters[$event->event][$convertDate])) {
                        $revenueCounters[$event->event][$convertDate] = 0;
                    }

                    $revenueCounters[$event->event][$convertDate] += intval($event->value);
                }
            }
        }

        return $revenueCounters;
    }

    /**
     * @param array $displayEvents
     * @param array $revenueCounters
     * @param int $uniqUsersCount
     * @return array
     */
    public function getPercentages(
        array $displayEvents,
        array $revenueCounters,
        int $uniqUsersCount
    ): array {
        $revenuePercentage = [];

        foreach ($displayEvents as $displayEvent) {
            if (!isset($revenueCounters[$displayEvent])) {
                $revenuePercentage[$displayEvent] = 0;
                continue;
            }

            $counter = array_sum(array_values($revenueCounters[$displayEvent]));

            if ($uniqUsersCount === 0) {
                $revenuePercentage[$displayEvent] = 0;
                continue;
            }

            $revenuePercentage[$displayEvent] = intval(($counter / $uniqUsersCount) * 100);
        }

        return $revenuePercentage;
    }
}
