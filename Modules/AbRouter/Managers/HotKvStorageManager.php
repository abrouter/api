<?php
declare(strict_types=1);

namespace Modules\AbRouter\Managers;

use Illuminate\Support\Facades\Redis;

class HotKvStorageManager
{
    public function store(string $key, string $data): bool
    {
        return Redis::connection()->client()->set($key, $data);
    }
}
