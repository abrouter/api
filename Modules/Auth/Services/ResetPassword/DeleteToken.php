<?php
declare(strict_types=1);

namespace Modules\Auth\Services\ResetPassword;

use Modules\Auth\Models\PasswordReset;
use Modules\Auth\Services\ResetPassword\DTO\ResetPasswordRequestDTO;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Throwable;

class DeleteToken
{
    /**
     * @param ResetPasswordRequestDTO $resetPasswordRequestDTO
     * @return 
     * @throws Throwable
     */
    public function delete(ResetPasswordRequestDTO $resetPasswordRequestDTO)
    {
        $delete = PasswordReset::where('email', $resetPasswordRequestDTO->getEmail())->delete();
        
        return $delete;
    }
}