<?php

namespace Modules\Auth\Http\Controllers;

use Exception;
use Illuminate\Routing\Controller;
use Modules\Auth\Http\Resources\AccessToken\AccessTokenResource;
use Modules\Auth\Http\Transformers\Auth\AuthTransformer;
use Modules\Auth\Services\Auth\Authenticator;
use Modules\Auth\Http\Requests\Auth\AuthUserRequest;
use Modules\Auth\Services\Auth\ShortTokenHandlerService;

class AuthController extends Controller
{
    /**
     * @param AuthUserRequest $request
     * @param AuthTransformer $transformer
     * @param Authenticator $authenticator
     * @param ShortTokenHandlerService $shortTokenHandler
     * @return AccessTokenResource
     * @throws Exception
     */
    public function auth(
        AuthUserRequest $request,
        AuthTransformer $transformer,
        Authenticator $authenticator,
        ShortTokenHandlerService $shortTokenHandler
    ) {
        $userAuthDTO = $transformer->transform($request);
        $userWithAccessToken = $authenticator->auth($userAuthDTO);
        $shortTokenHandler->handle($userWithAccessToken);

        return new AccessTokenResource($userWithAccessToken);
    }
}
