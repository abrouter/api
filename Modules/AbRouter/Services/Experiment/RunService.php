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
use Modules\AbRouter\Services\RelatedUser\RelatedUserIds;

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

    /**
     * @var RelatedUserIds
     */
    private $relatedUserIds;

    public function __construct(
        DiceService $diceService,
        ExperimentsRepository $experimentsRepository,
        UniqueUsersCountManager $uniqueUsersCountManager,
        PaywallService $paywallService,
        ExperimentIdResolver $idResolver,
        RelatedUserIds $relatedUserIds
    ) {
        $this->diceService = $diceService;
        $this->experimentsRepository = $experimentsRepository;
        $this->uniqueUsersCountManager = $uniqueUsersCountManager;
        $this->paywallService = $paywallService;
        $this->idResolver = $idResolver;
        $this->relatedUserIds = $relatedUserIds;
    }

    public function run(RunExperimentDTO $runExperimentDTO): ExperimentBranchUser
    {
        $experiment = $this
            ->idResolver
            ->getExperimentsByResolvedId(
                $runExperimentDTO->getExperimentId(),
                $runExperimentDTO->getOwnerId()
            );

        /**
        $userIds = $this
            ->relatedUserIds
            ->getRelatedUserIds(
                $runExperimentDTO->getOwnerId(),
                (array) $runExperimentDTO->getUserSignature()
            );
         */


        $user = (new ExperimentUsers())
            ->newQuery()
            ->where('owner_id', $runExperimentDTO->getOwnerId())
            ->where('user_signature', '=', $runExperimentDTO->getUserSignature())
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
            'owner_id' => $experiment->owner_id,
        ]);
        $experimentBranchUser->save();

        return $experimentBranchUser;
    }
}
