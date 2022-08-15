<?php

namespace Modules\Core\Interfaces\Stats;

use Illuminate\Support\Collection;

interface Stats
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
    ): array;

    /**
     * @param array $allDisplayEvents
     * @param array $counters
     * @param int $uniqUsersCount
     * @return array
     */
    public function getPercentages(
        array $allDisplayEvents,
        array $counters,
        int $uniqUsersCount
    ): array;
}
