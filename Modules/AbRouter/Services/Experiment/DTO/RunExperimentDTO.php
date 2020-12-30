<?php
declare(strict_types=1);

namespace Modules\AbRouter\Services\Experiment\DTO;

class RunExperimentDTO
{
    /**
     * @var int
     */
    private $ownerId;
    /**
     * @var string
     */
    private $userSignature;
    /**
     * @var string
     */
    private $experimentUid;

    public function __construct(
        int $ownerId,
        string $userSignature,
        string $experimentUid
    ) {
        $this->ownerId = $ownerId;
        $this->userSignature = $userSignature;
        $this->experimentUid = $experimentUid;
    }

    /**
     * @return string
     */
    public function getExperimentId(): string
    {
        return $this->experimentUid;
    }

    /**
     * @return string
     */
    public function getUserSignature(): string
    {
        return $this->userSignature;
    }

    /**
     * @return int
     */
    public function getOwnerId(): int
    {
        return $this->ownerId;
    }
}
