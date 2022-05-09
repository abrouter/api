<?php
declare(strict_types=1);

namespace Modules\Auth\Entities\User;

use Carbon\Carbon;
use Modules\Auth\Models\User\User;
use Modules\Core\EntityId\ResourceIdInterface;

class UserEntityDecorator
{
    /**
     * @var User $user
     */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getEntityId(): string
    {
        return $this->user->getEntityId();
    }

    public static function getType(): string
    {
        return User::getType();
    }

    public function getUsername(): string
    {
        return $this->user->username;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->user->created_at;
    }

    public function getUpdatedAt(): Carbon
    {
        return $this->user->updated_at;
    }

    public function getId(): ?int
    {
        return $this->user->id;
    }

    public function model(): User
    {
        return $this->user;
    }
}
