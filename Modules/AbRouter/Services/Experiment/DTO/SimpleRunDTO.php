<?php
declare(strict_types=1);

namespace Modules\AbRouter\Services\Experiment\DTO;

class SimpleRunDTO
{
    /**
     * @var string
     */
    private $experimentId;
    /**
     * @var string
     */
    private $token;
    /**
     * @var string
     */
    private $userId;

    public function __construct(string $experimentId, string $token, string $userId)
    {
        $this->experimentId = $experimentId;
        $this->token = $token;
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getExperimentId(): string
    {
        return $this->experimentId;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }
}
