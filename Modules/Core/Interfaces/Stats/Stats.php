<?php

namespace Modules\Core\Interfaces\Stats;

use Illuminate\Support\Collection;

interface Stats
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
    ): array;

    public function getPercentages(
        array $events,
        array $counters,
        int $uniqUsersCount
    ): array;
}
