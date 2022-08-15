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
    private $incrementalUniqueCounters;

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
     * @var Experiment
     */
    private $experiments;

    public function __construct(
        array $percentage,
        array $incrementalCounters,
        array $incrementalUniqueCounters,
        array $summarizationCounters,
        array $referrersCounters,
        array $referrersPercentage,
        Experiment $experiments = null
    ) {
        $this->percentage = $percentage;
        $this->incrementalCounters = $incrementalCounters;
        $this->incrementalUniqueCounters = $incrementalUniqueCounters;
        $this->summarizationCounters = $summarizationCounters;
        $this->referrersCounters = $referrersCounters;
        $this->referrersPercentage = $referrersPercentage;
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
    public function getIncrementalUniqueCounters(): array
    {
        return $this->incrementalUniqueCounters;
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
     * @return Experiment
     */
    public function getExperiments(): Experiment
    {
        return $this->experiments;
    }
}
