<?php
declare(strict_types=1);

namespace Modules\AbRouter\Services\Experiment;

use Modules\AbRouter\Models\Experiments\Experiment;
use Modules\AbRouter\Models\Experiments\ExperimentBranches;
use Modules\AbRouter\Models\Experiments\ExperimentBranchUser;
use Modules\AbRouter\Models\Experiments\ExperimentUsers;
use Modules\AbRouter\Repositories\Experiments\ExperimentsRepository;
use Modules\AbRouter\Services\Experiment\DTO\RunExperimentDTO;
use Modules\Core\EntityId\Encoder;

class RunService
{
    /**
     * @var DiceService
     */
    private $diceService;

    /**
     * @var ExperimentsRepository
     */
    private $experimentsRepository;

    public function __construct(
        DiceService $diceService, 
        ExperimentsRepository $experimentsRepository
    ) {
        $this->diceService = $diceService;
        $this->experimentsRepository = $experimentsRepository;
    }

    public function run(RunExperimentDTO $runExperimentDTO): ExperimentBranchUser
    {
        $checkId = preg_match('/^([A-Z0-9]{8})(-){1}([A-Z0-9]{4})(-){1}([A-Z0-9]{4})(-){1}([A-Z0-9]{8})$/', $runExperimentDTO->getExperimentId());

        if ($checkId) {
            $experiment = $this->experimentsRepository->getExperimentsById($runExperimentDTO->getExperimentId(), $runExperimentDTO->getOwnerId());
        } else $experiment = $this->experimentsRepository->getExperimentsByAlias($runExperimentDTO->getExperimentId(), $runExperimentDTO->getOwnerId());

        $user = (new ExperimentUsers())
            ->newQuery()
            ->where('user_signature', $runExperimentDTO->getUserSignature())
            ->where('owner_id', $runExperimentDTO->getOwnerId())
            ->first();

        if (empty($user)) {
            $user = new ExperimentUsers([
                'owner_id' => $runExperimentDTO->getOwnerId(),
                'config' => '{}',
                'user_signature' => $runExperimentDTO->getUserSignature(),
            ]);
            $user->save();
        }

        /**
         * @var ExperimentBranchUser $experimentBranchUser
         */
        $experimentBranchUser = (new ExperimentBranchUser())
            ->newQuery()
            ->where('experiment_user_id', $user->id)
            ->where('experiment_id', $experiment->id)
            ->first();

        if (!empty($experimentBranchUser)) {
            return $experimentBranchUser;
        }

        $experiment->branches->each(function (ExperimentBranches $experimentBranches) {
            $this->diceService->addSide((string) $experimentBranches->id, $experimentBranches->percent);
        });

        $experimentBranchUser = new ExperimentBranchUser([
            'experiment_user_id' => $user->id,
            'experiment_id' => $experiment->id,
            'experiment_branch_id' => $this->diceService->roll(),
        ]);
        $experimentBranchUser->save();

        return $experimentBranchUser;
    }
}
