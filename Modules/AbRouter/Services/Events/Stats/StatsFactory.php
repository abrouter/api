<?php

namespace Modules\AbRouter\Services\Events\Stats;

use Modules\Core\Interfaces\Stats\Stats;

class StatsFactory
{
    public function getStatsMethod(string $action): Stats
    {
        switch ($action) {
            case 'event':
                return new EventStats();
            case 'referrer':
                return new ReferrerStats();
            case 'revenue':
                return new RevenueStats();
            default:
                throw new \Exception('Unknown Stats Method');
        }
    }
}
