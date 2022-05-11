<?php
declare(strict_types=1);

namespace Modules\Auth\Http\Transformers\ResetPassword;

use Modules\Auth\Http\Requests\ResetPassword\ResetPasswordRequest;
use Modules\Core\Http\Transformers\BaseTransformer;
use Modules\Auth\Services\ResetPassword\DTO\ResetPasswordRequestDTO;

/**
 * Class ResetPasswordTransformer
 * @package Modules\Auth\Http\Transformers\ResetPassword
 * @property ResetPasswordRequest $request
 */
class ResetPasswordTransformer extends BaseTransformer
{
    /**
     * @param ResetPasswordRequest $request
     * @return ResetPasswordRequestDTO
     */
    public function transform($request): ResetPasswordRequestDTO
    {
        return new ResetPasswordRequestDTO(
            $request->getAttribute('email'),
            $request->getAttribute('password'),
            $request->getAttribute('token')
        );
    }
}