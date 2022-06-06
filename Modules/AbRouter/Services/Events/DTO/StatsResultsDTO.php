<?php
declare(strict_types =1);

namespace Modules\AbRouter\Services\Events\DTO;

use Modules\AbRouter\Models\Experiments\Experiment;

class StatsResultsDTO
{
    /**
     * @var array
     */
    private $percentage;
    
    /**
     * @var array
     */
    private $counters;

    /**
     * @var array
     */
    private $revenueCounters;

    /**
     * @var array
     */
    private $revenuePercentage;

    /**
     * @var array
     */
    private $referrersCounters;

    /**
     * @var array
     */
    private $referrersPercentage;

    /**
     * @var array
     */
    private $eventCountersWithDate;

    /**
     * @var Experiment
     */
    private $experiments;

    public function __construct(
        array $percentage,
        array $counters,
        array $revenueCounters,
        array $revenuePercentage,
        array $referrersCounters,
        array $referrersPercentage,
        array $eventCountersWithDate,
        Experiment $experiments = null
    ) {
        $this->percentage = $percentage;
        $this->counters = $counters;
        $this->revenueCounters = $revenueCounters;
        $this->revenuePercentage = $revenuePercentage;
        $this->referrersCounters = $referrersCounters;
        $this->referrersPercentage = $referrersPercentage;
        $this->eventCountersWithDate = $eventCountersWithDate;
        $this->experiments = $experiments;
    }
    
    /**
     * @return array
     */
    public function getCounters(): array
    {
        return $this->counters;
    }
    
    /**
     * @return array
     */
    public function getPercentage(): array
    {
        return $this->percentage;
    }

    /**
     * @return array
     */
    public function getReferrersCounters(): array
    {
        return $this->referrersCounters;
    }

    /**
     * @return array
     */
    public function getRevenueCounters(): array
    {
        return $this->revenueCounters;
    }

    /**
     * @return array
     */
    public function getRevenuePercentage(): array
    {
        return $this->revenuePercentage;
    }

    /**
     * @return array
     */
    public function getReferrersPercentage(): array
    {
        return $this->referrersPercentage;
    }

    /**
     * @return array
     */
    public function getEventCountersWithDate(): array
    {
        return $this->eventCountersWithDate;
    }

    /**
     * @return Experiment
     */
    public function getExperiments(): Experiment
    {
        return $this->experiments;
    }
}
