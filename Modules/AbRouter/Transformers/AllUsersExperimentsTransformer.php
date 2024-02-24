<?php

namespace Modules\AbRouter\Transformers;

use Illuminate\Support\Collection;
use Modules\AbRouter\Models\Experiments\ExperimentBranchUser;
use Modules\AbRouter\Models\Experiments\ExperimentUsers;

class AllUsersExperimentsTransformer
{
    public function getAllUsersExperiments(Collection $usersExperiments): array
    {
        $allUsersExperiments = [];

        foreach ($usersExperiments as $userExperiment) {
            /**
             * @var ExperimentUsers $userExperiment
             */
            $allUsersExperiments[$userExperiment->user_signature] = $this
                ->getExperiments($userExperiment->experimentBranchUsers);
        }

        return $allUsersExperiments;
    }

    private function getExperiments(Collection $experimentUsers): array
    {
        $experiments = [];

        foreach ($experimentUsers as $experimentUser) {
            /**
             * @var ExperimentBranchUser $experimentUser
             */
            $experiments[$experimentUser->experiment->uid] = $experimentUser->experimentBranch->uid;
        }

        return $experiments;
    }
}
