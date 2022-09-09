<?php

namespace Modules\AbRouter\Services\Experiment;

use Illuminate\Database\Eloquent\Collection;
use Modules\AbRouter\Models\Experiments\ExperimentBranchUser;
use Modules\AbRouter\Models\Experiments\ExperimentUsers;

class AllUsersExperimentsService
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

    public function getExperiments(Collection $experimentUsers): array
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
