<?php
declare(strict_types=1);

namespace Modules\AbRouter\Managers;

use Modules\AbRouter\Models\UserUsage;

class UniqueUsersCountManager
{
    public function increment(int $userId): void
    {
        UserUsage::query()->updateOrCreate(
            [
                'user_id' => $userId,
            ],
        )->increment('unique_users_count');
    }
}
