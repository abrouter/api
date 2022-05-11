<?php
declare(strict_types =1);

namespace Modules\AbRouter\Services\RelatedUser\DTO;

class AllRelatedUsersDTO
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
     * AllRelatedUserDTO constructor.
     *
     * @param int      $ownerId
     * @param string   $userId
     */
    public function __construct(
        int $ownerId,
        string $userId
    ) {
        $this->ownerId = $ownerId;
        $this->userId = $userId;
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
}
