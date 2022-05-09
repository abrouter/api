<?php
declare(strict_types=1);

namespace Modules\Auth\Http\Transformers\Users;

use Modules\Auth\Http\Requests\User\UserCreateRequest;
use Modules\Core\Http\Transformers\BaseTransformer;
use Modules\Auth\Services\Users\DTO\UserCreateDTO;

/**
 * Class UserTransformer
 * @package Modules\Auth\Http\Transformers\Users
 * @property UserCreateRequest $request
 */
class UserTransformer extends BaseTransformer
{
    /**
     * @param UserCreateRequest $request
     * @return UserCreateDTO
     */
    public function transform($request): UserCreateDTO
    {
        return new UserCreateDTO(
            $request->getAttribute('username'),
            $request->getAttribute('password')
        );
    }
}
