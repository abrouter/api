<?php

namespace Modules\Core\Interfaces\Stats;

use Illuminate\Support\Collection;

interface Stats
{
    /**
     * @param Collection $eventsList
     * @param array $uniqUsers
     * @param Collection $allDisplayEvents
     * @param bool $date
     * @return array
     */
    public function getCounters(
        Collection $eventsList,
        array $uniqUsers,
        Collection $allDisplayEvents,
        bool $date = false
    ): array;

    /**
     * @param Collection $allDisplayEvents
     * @param array $counters
     * @param int $uniqUsersCount
     * @return array
     */
    public function getPercentages(
        Collection $allDisplayEvents,
        array $counters,
        int $uniqUsersCount
    ): array;
}
