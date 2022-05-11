<?php
declare(strict_types=1);

namespace Modules\Auth\Services\Auth\DTO;

class GooglePayloadDTO
{
    /**
     * @var string
     */
    private $google_id;

    /**
     * @var string
     */
    private $username;

    public function __construct(string $google_id, string $username)
    {
        $this->google_id = $google_id;
        $this->username = $username;
    }

    public function getGoogleId(): string
    {
        return $this->google_id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}
