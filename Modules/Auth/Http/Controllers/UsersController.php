<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Auth\Exposable\AuthDecorator;
use Modules\Auth\Http\Requests\User\UserCreateRequest;
use Modules\Auth\Http\Resources\AccessToken\ExperimentBranchUserResource;
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
     * @return ExperimentBranchUserResource
     * @throws Throwable
     */
    public function create(UserCreateRequest $request, UserTransformer $transformer, Creator $creator)
    {
        $userCreateDTO = $transformer->transform($request);
        $userWithAccessToken = $creator->create($userCreateDTO);

        return new ExperimentBranchUserResource($userWithAccessToken);
    }

    public function me(AuthDecorator $authDecorator)
    {
        $user = $authDecorator->get()->model();
        return new UserResource($user);
    }
}
