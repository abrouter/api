<?php
declare(strict_types =1);

namespace Modules\AbRouter\Services\CustomizationEvent\DTO;

class CustomizationEventDTO
{
    /**
     * @var string
     */
    private $userId;
    
    /**
     * @var string
     */
    private $event_name;

    /**
     * @var string
     */
    private $order;
    
    /**
     * CustomizationEventDTO constructor.
     *
     * @param string $userId
     * @param string $event_name
     * @param string $order
     */
    public function __construct(
        int $userId,
        string $eventName,
        string $order
    ) {
        $this->userId = $userId;
        $this->eventName = $eventName;
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
    public function getOrder(): string
    {
        return $this->order;
    }
    
}
