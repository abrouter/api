<?php
declare(strict_types=1);

namespace Modules\AbRouter\Managers;

use Illuminate\Support\Facades\DB;
use Modules\AbRouter\Models\UserUsage;

class UniqueUsersCountManager
{
    public function increment(int $userId): void
    {
        UserUsage::query()->updateOrCreate(
            [
                'user_id' => $userId,
            ],
            [
                'unique_users_count' => DB::raw('unique_users_count + 1'),
            ]
        );
    }
}
