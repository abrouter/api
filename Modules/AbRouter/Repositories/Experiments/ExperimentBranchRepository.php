<?php
declare(strict_types=1);

namespace Modules\AbRouter\Repositories\Experiments;

use Modules\AbRouter\Models\Experiments\ExperimentBranches;
use Modules\Core\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class ExperimentBranchRepository extends BaseRepository
{
    public function getBranchById(int $branchId): ExperimentBranches
    {
        /**
         * @var ExperimentBranches $model
         */
        $model = $this->query()->find($branchId);

        return $model;
    }

    protected function getModel()
    {
        return new ExperimentBranches();
    }
}
