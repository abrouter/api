<?php
declare(strict_types =1);

namespace Modules\AbRouter\Services\CustomizationEvent\DTO;

class CustomizationEventDTO
{
    /**
     * @var int
     */
    private $userId;
    
    /**
     * @var string
     */
    private $eventName;

    /**
     * @var string
     */
    private $eventType;

    /**
     * @var string
     */
    private $order;

    /**
     * CustomizationEventDTO constructor.
     *
     * @param int $userId
     * @param string $eventName
     * @param string $eventType
     * @param string $order
     */
    public function __construct(
        int $userId,
        string $eventName,
        string $eventType,
        string $order
    ) {
        $this->userId = $userId;
        $this->eventName = $eventName;
        $this->eventType = $eventType;
        $this->order = $order;
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

    /**
     * @return string
     */
    public function getEventType(): string
    {
        return $this->eventType;
    }
    
    /**
     * @return string
     */
    public function getOrder(): string
    {
        return $this->order;
    }
    
}
