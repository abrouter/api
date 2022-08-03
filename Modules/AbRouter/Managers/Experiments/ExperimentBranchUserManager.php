<?php
declare(strict_types=1);

namespace Modules\AbRouter\Managers\Experiments;

use Modules\AbRouter\Models\Experiments\ExperimentBranchUser;

class ExperimentBranchUserManager
{
    /**
     * @param int $experimentId
     * @param int $experimentBranchId
     * @param int $experimentUserId
     * @return ExperimentBranchUser
     */
    public function createExperimentBranchUser(
        int $experimentId,
        int $experimentBranchId,
        int $experimentUserId
    ) {
        /**
         * @var ExperimentBranchUser $experimentUserBranch
         */
        $experimentUserBranch =  ExperimentBranchUser
            ::query()
            ->create([
                'experiment_id' => $experimentId,
                'experiment_branch_id' => $experimentBranchId,
                'experiment_user_id' => $experimentUserId
            ]);

        return $experimentUserBranch;
    }
}
