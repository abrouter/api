<?php
declare(strict_types=1);

namespace Modules\AbRouter\Repositories;

use Illuminate\Support\Collection;
use Modules\AbRouter\Transformers\HotKvStorageKeysTransformer;

class AllUsersExperimentsHotStorageRepository
{
    private HotKvStorageRepository $hotKvStorageRepository;

    private HotKvStorageKeysTransformer $hotKvStorageKeysTransformer;

    public function __construct(
        HotKvStorageRepository $hotKvStorageRepository,
        HotKvStorageKeysTransformer $hotKvStorageKeysTransformer
    ) {
        $this->hotKvStorageRepository = $hotKvStorageRepository;
        $this->hotKvStorageKeysTransformer = $hotKvStorageKeysTransformer;
    }

    public function get(int $ownerId): ?Collection
    {
        $data = $this->hotKvStorageRepository->get($this->hotKvStorageKeysTransformer->getAllUsersKey($ownerId));
        if (empty($data)) {
            return null;
        }

        return unserialize($data);
    }
}
