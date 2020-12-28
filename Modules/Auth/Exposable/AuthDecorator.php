<?php
declare(strict_types=1);

namespace Modules\Auth\Exposable;

use Modules\Auth\Entities\User\UserEntityDecorator;
use Modules\Auth\Models\User\User;

class AuthDecorator
{
    public function get(): UserEntityDecorator
    {
        return new UserEntityDecorator($this->user());
    }

    private function user(): User
    {
        return auth()->user();
    }
}
