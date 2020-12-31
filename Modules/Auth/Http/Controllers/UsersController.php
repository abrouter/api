<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Auth\Exposable\AuthDecorator;
use Modules\Auth\Http\Requests\User\UserCreateRequest;
use Modules\Auth\Http\Resources\AccessToken\AccessTokenResource;
use Modules\Auth\Http\Resources\User\UserResource;
use Modules\Auth\Http\Transformers\Users\UserTransformer;
use Modules\Auth\Services\Users\Creator;
use Throwable;

class UsersController extends Controller
{
    /**
     * @param UserCreateRequest $request
     * @param UserTransformer $transformer
     * @param Creator $creator
     * @return AccessTokenResource
     * @throws Throwable
     */
    public function create(UserCreateRequest $request, UserTransformer $transformer, Creator $creator)
    {
        $userCreateDTO = $transformer->transform($request);
        $userWithAccessToken = $creator->create($userCreateDTO);

        return new AccessTokenResource($userWithAccessToken);
    }

    public function me(AuthDecorator $authDecorator)
    {
        $user = $authDecorator->get()->model();
        return new UserResource($user);
    }
}
