<?php
declare(strict_types=1);

namespace Modules\AbRouter\Services\Experiment\DTO;

class ExperimentDTO
{
    /**
     * @var BranchDTO[]
     */
    private $branches;
    /**
     * @var string
     */
    private $owner;
    /**
     * @var array
     */
    private $config;
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $uid;

    /**
     * @var string|null
     */
    private $id;

     /**
     * @var string
     */
    private $isEnabled;

    /**
     * @var bool
     */
    private $isFeatureToggle;

    public function __construct(
        ?string $id,
        string $name,
        string $uid,
        bool $isEnabled,
        bool $isFeatureToggle,
        array $config,
        string $owner,
        BranchDTO...$branches
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->uid = $uid;
        $this->isEnabled = $isEnabled;
        $this->isFeatureToggle = $isFeatureToggle;
        $this->config = $config;
        $this->owner = $owner;
        $this->branches = $branches;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUid(): string
    {
        return $this->uid;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getOwner(): string
    {
        return $this->owner;
    }

    /**
     * @return BranchDTO[]
     */
    public function getBranches(): array
    {
        return $this->branches;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function getIsEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * @return bool
     */
    public function getIsFeatureToggle(): bool
    {
        return $this->isFeatureToggle;
    }
}
