<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Auth\Exposable\AuthDecorator;
use Modules\Auth\Http\Requests\User\UserCreateRequest;
use Modules\Auth\Http\Resources\AccessToken\AccessTokenResource;
use Modules\Auth\Http\Resources\User\UserResource;
use Modules\Auth\Http\Transformers\Users\UserTransformer;
use Modules\Auth\Services\Auth\ShortTokenHandlerService;
use Modules\Auth\Services\Users\Creator;
use Modules\Auth\Repositories\Auth\TokenRepository;
use Modules\Auth\Http\Resources\ShortToken\ShortTokenResource;
use Throwable;

class UsersController extends Controller
{
    /**
     * @param UserCreateRequest $request
     * @param UserTransformer $transformer
     * @param Creator $creator
     * @param ShortTokenHandlerService $shortTokenHandlerService
     * @return AccessTokenResource
     * @throws Throwable
     */
    public function create(
        UserCreateRequest $request,
        UserTransformer $transformer,
        Creator $creator,
        ShortTokenHandlerService $shortTokenHandlerService
    ) {
        $userCreateDTO = $transformer->transform($request);
        $userWithAccessToken = $creator->create($userCreateDTO);
        $shortTokenHandlerService->handle($userWithAccessToken);

        return new AccessTokenResource($userWithAccessToken);
    }

    public function me(AuthDecorator $authDecorator)
    {
        $user = $authDecorator->get()->model();
        return new UserResource($user);
    }

    public function getShortToken(
        AuthDecorator $authDecorator,
        TokenRepository $tokenRepository
    ) {
        $token = $tokenRepository->forUser($authDecorator->get()->getId())->last();
        return new ShortTokenResource($token);
    }
}
