<?php
declare(strict_types=1);

namespace Modules\AbRouter\Repositories\Experiments;

use Illuminate\Database\Eloquent\Collection;
use Modules\AbRouter\Models\Experiments\ExperimentBranchUser;
use Modules\Core\Repositories\BaseRepository;

class ExperimentBranchUserRepository extends BaseRepository
{
    public function getUsersId(int $branchId): Collection
    {
        /**
         * @var Collection $collection
         */
        $collection = $this->query()
            ->where('experiment_branch_id', $branchId)
            ->get();
        
        return $collection;
    }

    protected function getModel()
    {
        return new ExperimentBranchUser();
    }
}
