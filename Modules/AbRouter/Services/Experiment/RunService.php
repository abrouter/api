<?php
declare(strict_types=1);

namespace Modules\AbRouter\Services\Experiment;

use Modules\AbRouter\Managers\UniqueUsersCountManager;
use Modules\AbRouter\Models\Experiments\ExperimentBranches;
use Modules\AbRouter\Models\Experiments\ExperimentBranchUser;
use Modules\AbRouter\Models\Experiments\ExperimentUsers;
use Modules\AbRouter\Repositories\Experiments\ExperimentsRepository;
use Modules\AbRouter\Services\Experiment\DTO\RunExperimentDTO;
use Modules\AbRouter\Services\Marketing\PaywallService;
use Modules\AbRouter\Services\Experiment\ExperimentIdResolver;

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

    /**
     * @var UniqueUsersCountManager
     */
    private $uniqueUsersCountManager;

    /**
     * @var PaywallService
     */
    private $paywallService;

    /**
     * @var ExperimentIdResolver
     */
    private $idResolver;

    public function __construct(
        DiceService $diceService,
        ExperimentsRepository $experimentsRepository,
        UniqueUsersCountManager $uniqueUsersCountManager,
        PaywallService $paywallService,
        ExperimentIdResolver $idResolver
    ) {
        $this->diceService = $diceService;
        $this->experimentsRepository = $experimentsRepository;
        $this->uniqueUsersCountManager = $uniqueUsersCountManager;
        $this->paywallService = $paywallService;
        $this->idResolver = $idResolver;
    }

    public function run(RunExperimentDTO $runExperimentDTO): ExperimentBranchUser
    {
        $experiment = $this
            ->idResolver
            ->getExperimentsByResolvedId(
                $runExperimentDTO->getExperimentId(),
                $runExperimentDTO->getOwnerId()
            );

        $user = (new ExperimentUsers())
            ->newQuery()
            ->where('user_signature', $runExperimentDTO->getUserSignature())
            ->where('owner_id', $runExperimentDTO->getOwnerId())
            ->first();

        if (empty($user)) {
            //usage statistics
            $this->uniqueUsersCountManager->increment($runExperimentDTO->getOwnerId());
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

        $isRunningAllowed = $this->paywallService->isExperimentRunAllowed($runExperimentDTO->getOwnerId());
        $roll = $isRunningAllowed ? $this->diceService->roll() : $this->diceService->rollFirst();
        $experimentBranchUser = new ExperimentBranchUser([
            'experiment_user_id' => $user->id,
            'experiment_id' => $experiment->id,
            'experiment_branch_id' => $roll,
        ]);
        $experimentBranchUser->save();

        return $experimentBranchUser;
    }
}
