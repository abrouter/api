<?php
declare(strict_types =1);

namespace Modules\AbRouter\Services\Events\DTO;

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
    
    public function __construct(array $percentage, array $counters)
    {
        $this->percentage = $percentage;
        $this->counters = $counters;
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
}
