<?php
declare(strict_types=1);

namespace Modules\Auth\Services\Auth\DTO;

class AuthWithGoogleRequestDTO
{
    /**
     * @var string
     */
    private $id_token;

    public function __construct(string $id_token)
    {
        $this->id_token = $id_token;
    }

    public function getIdToken(): string
    {
        return $this->id_token;
    }
}
