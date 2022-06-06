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
     * @param array $allDisplayEvents
     * @param bool $enableDateCounter
     * @return array
     */
    public function getCounters(
        Collection $eventsList,
        array $uniqUsers,
        array $displayEvents,
        bool $enableDateCounter = false
    ): array {
        $eventCounters = [];

        foreach ($eventsList as $event) {
            /**
             * @var Event $event
             */

            if (in_array($event->event, $displayEvents, true)) {
                if (!isset($eventCounters[$event->event])) {
                    $eventCounters[$event->event] = 0;
                }

                $eventCounters[$event->event] += $event->value;
            }
        }

        return $eventCounters;
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

            $counter = $revenueCounters[$displayEvent];

            if ($uniqUsersCount === 0) {
                $revenuePercentage[$displayEvent] = 0;
                continue;
            }

            $revenuePercentage[$displayEvent] = intval(($counter / $uniqUsersCount) * 100);
        }

        return $revenuePercentage;
    }
}
