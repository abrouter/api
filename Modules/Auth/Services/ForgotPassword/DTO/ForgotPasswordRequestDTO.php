<?php
declare(strict_types=1);

namespace Modules\Auth\Services\ForgotPassword\DTO;

class ForgotPasswordRequestDTO
{
    /**
     * @var string
     */
    private $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
