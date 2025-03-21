<?php
declare(strict_types=1);

namespace Modules\AbRouter\Repositories;

use Illuminate\Support\Facades\Redis;

class HotKvStorageRepository
{
    /**
     * @param string $key
     * @return false|mixed|string
     */
    public function get(string $key)
    {
        return Redis::connection()->client()->get($key);
    }
}
