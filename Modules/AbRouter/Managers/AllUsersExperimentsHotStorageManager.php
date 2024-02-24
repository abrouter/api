<?php
declare(strict_types=1);

namespace Modules\AbRouter\Managers;

use Illuminate\Support\Collection;
use Modules\AbRouter\Transformers\HotKvStorageKeysTransformer;

class AllUsersExperimentsHotStorageManager
{
    private HotKvStorageManager $hotKvStorageManager;

    private HotKvStorageKeysTransformer $hotKvStorageKeysTransformer;

    public function __construct(
        HotKvStorageManager $hotKvStorageManager,
        HotKvStorageKeysTransformer $hotKvStorageKeysTransformer
    ) {
        $this->hotKvStorageManager = $hotKvStorageManager;
        $this->hotKvStorageKeysTransformer = $hotKvStorageKeysTransformer;
    }

    public function store(int $ownerId, Collection $collection)
    {
        $this->hotKvStorageManager->store(
            $this->hotKvStorageKeysTransformer->getAllUsersKey($ownerId),
            serialize($collection)
        );
    }
}
