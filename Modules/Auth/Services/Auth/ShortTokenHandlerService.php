<?php
declare(strict_types=1);

namespace Modules\Auth\Services\Auth;

use Modules\Auth\Entities\User\UserWithAccessToken;
use Modules\Auth\Models\User\UserShortToken;

class ShortTokenHandlerService
{
    public function handle(UserWithAccessToken $userWithAccessToken): UserShortToken
    {
        $model = (new UserShortToken())->newQuery()->where('user_id', $userWithAccessToken->getUser()->id)->first();
        if (!$model) {
            $model = new UserShortToken();
        }

        $model->fill([
            'user_id' => $userWithAccessToken->getUser()->id,
            'token' => $userWithAccessToken->getAccessToken()->getToken(),
        ]);
        $model->save();

        return $model;
    }
}
