<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Auth\Http\Requests\ForgotPassword\ForgotPasswordRequest;
use Modules\Auth\Http\Transformers\ForgotPassword\ForgotPasswordTransformer;
use Modules\Auth\Services\ForgotPassword\VerificationEmail;
use Modules\Auth\Services\ForgotPassword\SendResetLink;
use Throwable;

class ForgotPasswordController extends Controller
{
    /**
     * @param ForgotPasswordRequest $request
     * @param ForgotPasswordTransformer $transformer
     * @param VerificationEmail $verificationEmail
     * @param SendResetLink $sendResetLink
     * @throws Throwable
     */
    public function restore(
        ForgotPasswordRequest $request,
        ForgotPasswordTransformer $transformer,
        VerificationEmail $verificationEmail,
        SendResetLink $sendResetLink
        )
    {
        $forgotPasswordDTO = $transformer->transform($request);
        $verification = $verificationEmail->verification($forgotPasswordDTO);
        
        return $sendLink = $sendResetLink->sendResetPasswordLink($verification);
    }

   
}
