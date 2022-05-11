<?php
declare(strict_types =1);

namespace Modules\AbRouter\Services\Events\DTO;

class AllEventsDTO
{
    /**
     * @var int
     */
    private $ownerId;

    /**
     * @var string
     */
    private $userId;

    /**
     * AllEventsDTO constructor.
     *
     * @param int    $ownerId
     * @param string $userId
     */
    public function __construct(
        int $ownerId,
        string $userId
    ) {
        $this->ownerId = $ownerId;
        $this->userId = $userId;
    }

    /**
     * @return int
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
}
