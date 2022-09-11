<?php

namespace Modules\AbRouter\Transformers;

class HotKvStorageKeysTransformer
{
    public function getAllUsersKey(int $ownerId): string
    {
        return sprintf('all-users-'  . $ownerId);
    }
}
