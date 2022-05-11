<?php
declare(strict_types =1);

namespace Modules\AbRouter\Services\RelatedUser\DTO;

class RelatedUserDTO
{
    /**
     * @var string
     */
    private $ownerId;
    
    /**
     * @var string
     */
    private $userId;
    
    /**
     * @var string
     */
    private $relatedUserId;
    
    /**
     * @var int|null
     */
    private $eventId;
    
    /**
     * RelatedUserDTO constructor.
     *
     * @param int      $ownerId
     * @param string   $userId
     * @param string   $relatedUserId
     * @param int|null $eventId
     */
    public function __construct(
        int $ownerId,
        string $userId,
        string $relatedUserId,
        ?int $eventId = null
    ) {
        $this->ownerId = $ownerId;
        $this->userId = $userId;
        $this->relatedUserId = $relatedUserId;
        $this->eventId = $eventId;
    }
    
    /**
     * @return string
     */
    public function getOwnerId(): int
    {
        return $this->ownerId;
    }
    
    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }
    
    /**
     * @return string
     */
    public function getRelatedUserId(): string
    {
        return $this->relatedUserId;
    }
    
    /**
     * @return int|null
     */
    public function getEventId(): ?int
    {
        return $this->eventId;
    }
}
