<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Auth\Http\Requests\ResetPassword\ResetPasswordRequest;
use Modules\Auth\Http\Transformers\ResetPassword\ResetPasswordTransformer;
use Modules\Auth\Services\ResetPassword\VerificationToken;
use Modules\Auth\Services\ResetPassword\DeleteToken;
use Modules\Auth\Services\ResetPassword\ResetPassword;
use Throwable;

class ResetPasswordController extends Controller
{
    /**
     * @param ResetPasswordRequest $request
     * @param ResetPasswordTransformer $transformer
     * @param VerificationToken $verificationToken
     * @throws Throwable
     */
    public function reset(
        ResetPasswordRequest $request,
        ResetPasswordTransformer $transformer,
        VerificationToken $verificationToken,
        DeleteToken $deleteToken,
        ResetPassword $resetPassword
        )
    {
        $resetPasswordDTO = $transformer->transform($request);
        $verification = $verificationToken->verification($resetPasswordDTO);
        $delete = $deleteToken->delete($resetPasswordDTO);
        $reset = $resetPassword->reset($resetPasswordDTO);

        return json_encode($reset);
        
    }

   
}
