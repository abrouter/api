<?php

namespace Modules\Auth\Http\Controllers;

use AbRouter\JsonApiFormatter\DataSource\DataProviders\SimpleDataProvider;
use Exception;
use Illuminate\Routing\Controller;
use Modules\Auth\Http\Resources2\AccessTokenScheme;
use Modules\Auth\Http\Transformers\Auth\AuthTransformer;
use Modules\Auth\Http\Transformers\Auth\AuthWithGoogleTransformer;
use Modules\Auth\Http\Transformers\Auth\GooglePayloadTransformer;
use Modules\Auth\Services\Auth\Authenticator;
use Modules\Auth\Services\Auth\CheckIdToken;
use Modules\Auth\Services\Auth\AuthenticatorWithGoogle;
use Modules\Auth\Http\Requests\Auth\AuthUserRequest;
use Modules\Auth\Http\Requests\Auth\AuthWithGoogleRequest;
use Modules\Auth\Services\Auth\ShortTokenHandlerService;

class AuthController extends Controller
{
    /**
     * @param AuthUserRequest $request
     * @param AuthTransformer $transformer
     * @param Authenticator $authenticator
     * @param ShortTokenHandlerService $shortTokenHandler
     * @return AccessTokenScheme
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

        return new AccessTokenScheme(new SimpleDataProvider($userWithAccessToken));
    }

    /**
     * @param AuthWithGoogleRequest $request
     * @param AuthWithGoogleTransformer $transformer
     * @param GooglePayloadTransformer $googlePayloadTransformer
     * @param CheckIdToken $checkId
     * @param AuthenticatorWithGoogle $authenticator
     * @param ShortTokenHandlerService $shortTokenHandler
     * @return AccessTokenScheme
     * @throws Exception
     */
    public function authWithGoogle (
        AuthWithGoogleRequest $request,
        AuthWithGoogleTransformer $transformer,
        CheckIdToken $checkId,
        GooglePayloadTransformer $googlePayloadTransformer,
        AuthenticatorWithGoogle $authenticator,
        ShortTokenHandlerService $shortTokenHandler
    ) {
        $idTokenDTO = $transformer->transform($request);
        $googlePayload = $checkId->checkId($idTokenDTO);
        $googlePayloadDTO = $googlePayloadTransformer->transform($googlePayload);
        $userWithAccessToken = $authenticator->authOrCreate($googlePayloadDTO);
        $shortTokenHandler->handle($userWithAccessToken);

        return new AccessTokenScheme(new SimpleDataProvider($userWithAccessToken));
    }
}
