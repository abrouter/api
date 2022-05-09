<?php
declare(strict_types=1);

namespace Modules\Auth\Services\ResetPassword;

use Modules\Auth\Models\PasswordReset;
use Modules\Auth\Repositories\ForgotPassword\ForgotPasswordRepository;
use Modules\Auth\Services\ResetPassword\DTO\ResetPasswordRequestDTO;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Throwable;

class VerificationToken
{
    /**
     * @var ForgotPasswordRepository
     */
    private $forgotPasswordRepository;

    public function __construct(ForgotPasswordRepository $forgotPasswordRepository)
    {
        $this->forgotPasswordRepository = $forgotPasswordRepository;
    }

    /**
     * @param ResetPasswordRequestDTO $resetPasswordRequestDTO
     * @return 
     * @throws Throwable
     */
    public function verification(ResetPasswordRequestDTO $resetPasswordRequestDTO)
    {
        if (!$this->forgotPasswordRepository->hasUserWithEmail($resetPasswordRequestDTO->getEmail())) {
            throw new UnprocessableEntityHttpException('No password reset link was sent to this email');
        }

        $verificationToken = PasswordReset::where('email', $resetPasswordRequestDTO->getEmail())->value('token');
        
        if($resetPasswordRequestDTO->getToken() !== $verificationToken){
            throw new UnprocessableEntityHttpException('Tokens do not match');
        }
             
        return true;
    }
}