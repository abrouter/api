<?php
declare(strict_types=1);

namespace Modules\Auth\Http\Transformers\Auth;

use Modules\Auth\Http\Requests\Auth\AuthUserRequest;
use Modules\Auth\Http\Requests\User\UserCreateRequest;
use Modules\Core\Http\Transformers\BaseTransformer;
use Modules\Auth\Services\Auth\DTO\AuthRequestDTO;

/**
 * Class UserTransformer
 * @package Modules\Auth\Http\Transformers\Users
 * @property UserCreateRequest $request
 */
class AuthTransformer extends BaseTransformer
{
    /**
     * @param AuthUserRequest $request
     * @return AuthRequestDTO
     */
    public function transform($request): AuthRequestDTO
    {
        return new AuthRequestDTO(
            $request->getAttribute('username'),
            $request->getAttribute('password')
        );
    }
}
