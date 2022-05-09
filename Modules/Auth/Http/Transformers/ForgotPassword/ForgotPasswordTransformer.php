<?php
declare(strict_types=1);

namespace Modules\Auth\Http\Transformers\ForgotPassword;

use Modules\Auth\Http\Requests\ForgotPassword\ForgotPasswordRequest;
use Modules\Core\Http\Transformers\BaseTransformer;
use Modules\Auth\Services\ForgotPassword\DTO\ForgotPasswordRequestDTO;

/**
 * Class ForgotPasswordTransformer
 * @package Modules\Auth\Http\Transformers\ForgotPassword
 * @property ForgotPasswordRequest $request
 */
class ForgotPasswordTransformer extends BaseTransformer
{
    /**
     * @param ForgotPasswordRequest $request
     * @return ForgotPasswordRequestDTO
     */
    public function transform($request): ForgotPasswordRequestDTO
    {
        return new ForgotPasswordRequestDTO(
            $request->getAttribute('email')
        );
    }
}