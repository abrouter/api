<?php
declare(strict_types=1);

namespace Modules\AbRouter\Services\Experiment\DTO;

class BranchDTO
{
    /**
     * @var string
     */
    private $uid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $percent;

    /**
     * @var array
     */
    private $config;

    /**
     * @var string
     */
    private $owner;

    /**
     * @var string|null
     */
    private $id;

    public function __construct(?string $id, string $name, string $uid, int $percent, string $owner, array $config)
    {
        $this->id = $id;
        $this->name = $name;
        $this->uid = $uid;
        $this->percent = $percent;
        $this->config = $config;
        $this->owner = $owner;
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
     * @return int
     */
    public function getPercent(): int
    {
        return $this->percent;
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

    public function getId(): ?string
    {
        return $this->id;
    }
}
