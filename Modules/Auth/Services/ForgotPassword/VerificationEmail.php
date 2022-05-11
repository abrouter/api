<?php
declare(strict_types=1);

namespace Modules\Auth\Services\ForgotPassword;

use Modules\Auth\Models\User\User;
use Modules\Auth\Repositories\User\UserRepository;
use Modules\Auth\Services\ForgotPassword\DTO\ForgotPasswordRequestDTO;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Throwable;

class VerificationEmail
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
     * @param ForgotPasswordRequestDTO $forgotPasswordRequestDTO
     * @return 
     * @throws Throwable
     */
    public function verification(ForgotPasswordRequestDTO $forgotPasswordRequestDTO)
    {
        if (!$this->userRepository->hasUserWithUsername($forgotPasswordRequestDTO->getEmail())) {
            throw new UnprocessableEntityHttpException('There is no user with this username');
        }
        
        return $forgotPasswordRequestDTO->getEmail();
    }
}