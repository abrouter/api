<?php
declare(strict_types=1);

namespace Modules\Auth\Repositories\ForgotPassword;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Auth\Models\PasswordReset;
use Modules\Core\Repositories\BaseRepository;

class ForgotPasswordRepository extends BaseRepository
{
    public function getOneByEmail(string $email): PasswordReset
    {
        /**
         * @var PasswordResset $model
         */
        $model = $this->query()->where('email', $email)->firstOrFail();
        return $model;
    }

    public function hasUserWithEmail(string $email): bool
    {
        try {
            $this->getOneByEmail($email);
        } catch (ModelNotFoundException $notFoundException) {
            return false;
        }

        return true;
    }

    protected function getModel()
    {
        return new PasswordReset();
    }    
}
