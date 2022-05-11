<?php
declare(strict_types=1);

namespace Modules\AbRouter\Managers\Experiments;

use Modules\AbRouter\Models\Experiments\ExperimentBranchUser;

class ExperimentBranchUserManager
{
    public function createExperimentBranchUser(
        int $experimentId,
        int $experimentBranchId,
        int $experimentUserId
    ) {
        return ExperimentBranchUser
            ::query()
            ->create([
                'experiment_id' => $experimentId,
                'experiment_branch_id' => $experimentBranchId,
                'experiment_user_id' => $experimentUserId
            ]);

    }
}
