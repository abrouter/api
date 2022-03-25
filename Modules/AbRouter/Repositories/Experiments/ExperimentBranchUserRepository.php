<?php
declare(strict_types=1);

namespace Modules\AbRouter\Repositories\Experiments;

use Illuminate\Database\Eloquent\Collection;
use Modules\AbRouter\Models\Experiments\ExperimentBranchUser;
use Modules\Core\Repositories\BaseRepository;

class ExperimentBranchUserRepository extends BaseRepository
{
    public function getUsersIdByBranchId(int $branchId): Collection
    {
        /**
         * @var Collection $collection
         */
        return $this->query()
            ->where('experiment_branch_id', $branchId)
            ->get();
    }

    public function getUsersIdByExperimentId(int $experimentId): Collection
    {
        /**
         * @var Collection $collection
         */
        return $this->query()
            ->where('experiment_id', $experimentId)
            ->get();
    }

    protected function getModel(): ExperimentBranchUser
    {
        return new ExperimentBranchUser();
    }
}
