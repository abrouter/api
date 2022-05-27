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

    public function getPercentages(
        Collection $allDisplayEvents,
        array $counters,
        int $uniqUsersCount
    ): array;
}
