<?php

namespace Modules\Auth\Http\Controllers;

use AbRouter\JsonApiFormatter\DataSource\DataProviders\SimpleDataProvider;
use Illuminate\Routing\Controller;
use Modules\Auth\Exposable\AuthDecorator;
use Modules\Auth\Http\Requests\User\UserCreateRequest;
use Modules\Auth\Http\Resources2\AccessTokenScheme;
use Modules\Auth\Http\Resources2\ShortTokenScheme;
use Modules\Auth\Http\Transformers\Users\UserTransformer;
use Modules\Auth\Services\Auth\ShortTokenHandlerService;
use Modules\Auth\Services\Users\Creator;
use Modules\Auth\Repositories\Auth\TokenRepository;
use Throwable;
use Modules\Auth\Http\Resources2\UserScheme;

class UsersController extends Controller
{
    /**
     * @param UserCreateRequest $request
     * @param UserTransformer $transformer
     * @param Creator $creator
     * @param ShortTokenHandlerService $shortTokenHandlerService
     * @return AccessTokenScheme
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

        return new AccessTokenScheme(new SimpleDataProvider($userWithAccessToken));
    }

    public function me(
        AuthDecorator $authDecorator,
        TokenRepository $tokenRepository
    ) {
        $user = $authDecorator->get()->model();
        $token = $tokenRepository->forUser($authDecorator->get()->getId())->last();
        $token = empty($token) ? '' : $token->id;


        return (new UserScheme(new SimpleDataProvider($user)))->addMeta([
            'short_token' => $token,
        ]);
    }

    public function getShortToken(
        AuthDecorator $authDecorator,
        TokenRepository $tokenRepository
    ) {
        $token = $tokenRepository->forUser($authDecorator->get()->getId())->last();
        return new ShortTokenScheme(new SimpleDataProvider($token));
    }
}
