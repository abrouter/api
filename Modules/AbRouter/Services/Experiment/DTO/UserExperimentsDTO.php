<?php
declare(strict_types=1);

namespace Modules\AbRouter\Services\Experiment\DTO;

class UserExperimentsDTO
{
    /**
     * @var int
     */
    private $owner;

    /**
     * @var string
     */
    private $experimentId;

    /**
     * @var string
     */
    private $userSignature;

    /**
     * @var string
     */
    private $experimentBranchId;

    /**
     * @var bool $force
     */
    private bool $force;

    public function __construct(
        int $owner,
        string $userSignature,
        string $experimentId,
        string $experimentBranchId,
        bool $force
    ) {
        $this->owner = $owner;
        $this->userSignature = $userSignature;
        $this->experimentId = $experimentId;
        $this->experimentBranchId = $experimentBranchId;
        $this->force = $force;
    }

    /**
     * @return int
     */
    public function getOwner(): int
    {
        return $this->owner;
    }

    /**
     * @return int
     */
    public function getExperimentId(): string
    {
        return $this->experimentId;
    }

    /**
     * @return string
     */
    public function getUserSignature(): string
    {
        return $this->userSignature;
    }

    public function getExperimentBranchId(): string
    {
        return $this->experimentBranchId;
    }

    /**
     * @return bool
     */
    public function isForce(): bool
    {
        return $this->force;
    }
}
