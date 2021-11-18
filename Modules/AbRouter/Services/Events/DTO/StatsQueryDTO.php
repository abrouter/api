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
    
    public function __construct(int $ownerId, ?string $tag)
    {
        $this->ownerId = $ownerId;
        $this->tag = $tag;
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
    
    public function hasTag(): bool
    {
        return $this->tag !== null;
    }
}
