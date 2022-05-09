<?php
declare(strict_types=1);

namespace Modules\Auth\Services\ForgotPassword;

use Modules\Auth\Models\PasswordReset;
use Modules\Auth\Repositories\ForgotPassword\ForgotPasswordRepository;
use Modules\Auth\Emails\SendResetPasswordLink;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Exception;

class SendResetLink
{
    /**
     * @var ForgotPasswordRepository
     */
    private $forgotPasswordRepository;

    public function __construct(ForgotPasswordRepository $forgotPasswordRepository)
    {
        $this->forgotPasswordRepository = $forgotPasswordRepository;
    }

    public function sendResetPasswordLink($email)
    {
        $token = Str::random(60);

        if($this->forgotPasswordRepository->hasUserWithEmail($email))
        {
            throw new Exception('A password reset message has already been sent to this email');
        }
        
        $saveToken = new PasswordReset([
            'email' => $email,
            'token' => $token
        ]);

        $saveToken->saveOrFail();

        $mail = Mail::to($email)->send(new SendResetPasswordLink($token, $email));

        return json_encode('Mail send');
    }
}