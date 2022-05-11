<?php
declare(strict_types=1);

namespace Modules\Auth\Services\ResetPassword\DTO;

class ResetPasswordRequestDTO
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $token;

    public function __construct(string $email, string $password, string $token)
    {
        $this->email = $email;
        $this->password = $password;
        $this->token = $token;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
