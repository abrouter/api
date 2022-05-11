<?php
declare(strict_types=1);

namespace Modules\Auth\Services\ResetPassword;

use Modules\Auth\Models\User\User;
use Modules\Auth\Repositories\User\UserRepository;
use Modules\Auth\Services\ResetPassword\DTO\ResetPasswordRequestDTO;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Throwable;

class ResetPassword
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param ResetPasswordRequestDTO $resetPasswordRequestDTO
     * @throws Throwable
     */
    public function reset(ResetPasswordRequestDTO $resetPasswordRequestDTO)
    {
        if (!$this->userRepository->hasUserWithUsername($resetPasswordRequestDTO->getEmail())) {
            throw new UnprocessableEntityHttpException('There is no user with this username');
        }

        $resetPassword = User::where('username', $resetPasswordRequestDTO->getEmail())
                            ->update(['password' => encrypt($resetPasswordRequestDTO->getPassword())]);
        
        if(!$resetPassword){
            throw new UnprocessableEntityHttpException('Failed to change password, please try again');
        }

        return 'Password changed successfully';
    }
}