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
     * @var int|null
     */
    private $experimentBranchId;
    
    public function __construct(int $ownerId, ?string $tag, ?int $experimentBranchId)
    {
        $this->ownerId = $ownerId;
        $this->tag = $tag;
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
     * @return int
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
