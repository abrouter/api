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
    private $incrementalCounters;

    /**
     * @var array
     */
    private $summarizationCounters;

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
        array $incrementalCounters,
        array $summarizationCounters,
        array $referrersCounters,
        array $referrersPercentage,
        array $eventCountersWithDate,
        Experiment $experiments = null
    ) {
        $this->percentage = $percentage;
        $this->incrementalCounters = $incrementalCounters;
        $this->summarizationCounters = $summarizationCounters;
        $this->referrersCounters = $referrersCounters;
        $this->referrersPercentage = $referrersPercentage;
        $this->eventCountersWithDate = $eventCountersWithDate;
        $this->experiments = $experiments;
    }
    
    /**
     * @return array
     */
    public function getIncrementalCounters(): array
    {
        return $this->incrementalCounters;
    }

    /**
     * @return array
     */
    public function getSummarizationCounters(): array
    {
        return $this->summarizationCounters;
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
