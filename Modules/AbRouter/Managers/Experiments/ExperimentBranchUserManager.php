<?php
declare(strict_types=1);

namespace Modules\AbRouter\Managers\Experiments;

use Modules\AbRouter\Models\Experiments\Experiment;
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
         * @var Experiment $exp
         */
        $exp = Experiment::query()->find($experimentId);

        /**
         * @var ExperimentBranchUser $experimentUserBranch
         */
        $experimentUserBranch =  ExperimentBranchUser
            ::query()
            ->create([
                'experiment_id' => $experimentId,
                'experiment_branch_id' => $experimentBranchId,
                'experiment_user_id' => $experimentUserId,
                'owner_id' => $exp->owner_id,
            ]);

        return $experimentUserBranch;
    }

    /**
     * @param int $experimentId
     * @param int $experimentBranchId
     * @param array $experimentUserIds
     * @return ExperimentBranchUser
     */
    public function updateExperimentBranchUser(
        int $experimentId,
        int $experimentBranchId,
        array $experimentUserIds
    ) {
        /**
         * @var ExperimentBranchUser $experimentUserBranch
         */
        $experimentUserBranch =  ExperimentBranchUser
            ::query()
            ->where('experiment_id', $experimentId)
            ->whereIn('experiment_user_id', $experimentUserIds)
            ->update([
                'experiment_branch_id' => $experimentBranchId
            ]);

        return $experimentUserBranch;
    }
}
