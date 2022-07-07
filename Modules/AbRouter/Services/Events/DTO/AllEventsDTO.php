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
     * @var ?string
     */
    private $dateFrom;

    /**
     * @var ?string
     */
    private $dateTo;

    /**
     * AllEventsDTO constructor.
     *
     * @param int    $ownerId
     * @param string $userId
     * @param ?string $dateFrom
     * @param ?string $dateTo
     */
    public function __construct(
        int $ownerId,
        string $userId,
        ?string $dateFrom,
        ?string $dateTo
    ) {
        $this->ownerId = $ownerId;
        $this->userId = $userId;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
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

    /**
     * @return ?string
     */
    public function getDateFrom(): ?string
    {
        return $this->dateFrom;
    }

    /**
     * @return ?string
     */
    public function getDateTo(): ?string
    {
        return $this->dateTo;
    }
}
