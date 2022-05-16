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
    private $type;

    /**
     * @var string
     */
    private $order;

    /**
     * CustomizationEventDTO constructor.
     *
     * @param int $userId
     * @param string $eventName
     * @param string $type
     * @param string $order
     */
    public function __construct(
        int $userId,
        string $eventName,
        string $type,
        string $order
    ) {
        $this->userId = $userId;
        $this->eventName = $eventName;
        $this->type = $type;
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
    public function getType(): string
    {
        return $this->type;
    }
    
    /**
     * @return string
     */
    public function getOrder(): string
    {
        return $this->order;
    }
    
}
