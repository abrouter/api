<?php
declare(strict_types =1);

namespace Modules\AbRouter\Services\Events\DTO;

class StatsQueryDTO
{
    /**
     * @var int
     */
    private $ownerId;
    
    /**
     * @var string|null
     */
    private $tag;

    /**
     * @var string|null
     */
    private $dateFrom;

    /**
     * @var string|null
     */
    private $dateTo;

    /**
     * @var string|null
     */
    private $experimentId;

    /**
     * @var int|null
     */
    private $experimentBranchId;
    
    public function __construct(
        int $ownerId,
        ?string $tag,
        ?string $dateFrom,
        ?string $dateTo,
        ?string $experimentId = null,
        ?int $experimentBranchId = null
    ) {
        $this->ownerId = $ownerId;
        $this->tag = $tag;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->experimentId = $experimentId;
        $this->experimentBranchId = $experimentBranchId;
    }
    
    /**
     * @return int
     */
    public function getOwnerId(): int
    {
        return $this->ownerId;
    }
    
    /**
     * @return string|null
     */
    public function getTag(): ?string
    {
        return $this->tag;
    }

    /**
     * @return string|null
     */
    public function getDateFrom(): ?string
    {
        return $this->dateFrom;
    }

    /**
     * @return string|null
     */
    public function getDateTo(): ?string
    {
        return $this->dateTo;
    }

    /**
     * @return string|null
     */
    public function getExperimentId(): ?string
    {
        return $this->experimentId;
    }

    /**
     * @return int|null
     */
    public function getExperimentBranchId(): ?int
    {
        return $this->experimentBranchId;
    }
    
    public function hasTag(): bool
    {
        return $this->tag !== null;
    }
}
