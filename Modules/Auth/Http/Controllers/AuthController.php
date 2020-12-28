<?php

namespace Modules\Auth\Http\Controllers;

use Exception;
use Illuminate\Routing\Controller;
use Modules\Auth\Http\Resources\AccessToken\AccessTokenResource;
use Modules\Auth\Http\Transformers\Auth\AuthTransformer;
use Modules\Auth\Services\Auth\Authenticator;
use Modules\Auth\Http\Requests\Auth\AuthUserRequest;

class AuthController extends Controller
{
    /**
     * @param AuthUserRequest $request
     * @param AuthTransformer $transformer
     * @param Authenticator $authenticator
     * @return AccessTokenResource
     * @throws Exception
     */
    public function auth(AuthUserRequest $request, AuthTransformer $transformer, Authenticator $authenticator)
    {
        $userAuthDTO = $transformer->transform($request);
        $userWithAccessToken = $authenticator->auth($userAuthDTO);

        return new AccessTokenResource($userWithAccessToken);
    }
}
