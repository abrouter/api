<?php
declare(strict_types =1);

namespace Modules\AbRouter\Services\CustomizationEvent\DTO;

class CustomizationEventUpdateDTO
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $userId;
    
    /**
     * @var string
     */
    private $eventName;
    
    /**
     * CustomizationEventDTO constructor.
     *
     * @param int $id
     * @param int $userId
     * @param string $eventName
     */
    public function __construct(
        int $id,
        int $userId,
        string $eventName
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->eventName = $eventName;
    }
    
    /**
     * @return string
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUserId(): int
    {
        return $this->userId;
    }
    
    /**
     * @return string
     */
    public function getEventName(): string
    {
        return $this->eventName;
    }
}
