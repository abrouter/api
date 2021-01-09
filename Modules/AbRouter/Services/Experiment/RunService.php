<?php
declare(strict_types=1);

namespace Modules\AbRouter\Services\Experiment;

use Modules\AbRouter\Models\Experiments\Experiment;
use Modules\AbRouter\Models\Experiments\ExperimentBranches;
use Modules\AbRouter\Models\Experiments\ExperimentBranchUser;
use Modules\AbRouter\Models\Experiments\ExperimentUsers;
use Modules\AbRouter\Services\Experiment\DTO\RunExperimentDTO;
use Modules\Core\EntityId\Encoder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RunService
{
    /**
     * @var DiceService
     */
    private $diceService;

    public function __construct(DiceService $diceService)
    {
        $this->diceService = $diceService;
    }

    public function run(RunExperimentDTO $runExperimentDTO): ExperimentBranchUser
    {
        try {
            $expInternalId = (new Encoder())->decode($runExperimentDTO->getExperimentId(), 'experiments');
        } catch (\Throwable $e) {
            throw new NotFoundHttpException('Failed to find an experiment');
        }

        /**
         * @var Experiment $experiment
         */
        $experiment = (new Experiment())
            ->newQuery()
            ->where('id', $expInternalId)
            ->where('owner_id', $runExperimentDTO->getOwnerId())
            ->first();
        if (empty($experiment)) {
            throw new NotFoundHttpException('Failed to find experiment with given uid');
        }

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
