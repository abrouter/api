<?php
declare(strict_types=1);

namespace Modules\AbRouter\Services\Experiment;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\AbRouter\Models\Experiments\ExperimentBranches;
use Modules\AbRouter\Models\Experiments\ExperimentBranchUser;
use Modules\AbRouter\Models\Experiments\ExperimentUsers;
use Modules\AbRouter\Repositories\Experiments\ExperimentBranchUserRepository;
use Modules\AbRouter\Repositories\Experiments\ExperimentUsersRepository;
use Modules\AbRouter\Managers\Experiments\ExperimentBranchUserManager;
use Modules\AbRouter\Services\Experiment\DTO\UserExperimentsDTO;
use Modules\Core\EntityId\EntityEncoder;

class UserExperimentsService
{
    /**
     * @var ExperimentUsersRepository
     */
    private $experimentUsersRepository;

    /**
     * @var ExperimentBranchUserRepository
     */
    private $branchUserRepository;

    /**
     * @var ExperimentBranchUserManager
     */
    private $branchUserManager;

    /**
     * @var ExperimentIdResolver
     */
    private $idResolver;

    public function __construct(
        ExperimentBranchUserRepository $branchUserRepository,
        ExperimentUsersRepository $experimentUsersRepository,
        ExperimentBranchUserManager $branchUserManager,
        ExperimentIdResolver $idResolver
    ) {
        $this->branchUserRepository = $branchUserRepository;
        $this->experimentUsersRepository = $experimentUsersRepository;
        $this->branchUserManager = $branchUserManager;
        $this->idResolver = $idResolver;
    }

    /**
     * @param UserExperimentsDTO $userExperimentDTO
     * @return ExperimentBranchUser
     * @throws \Exception
     */
    public function addUserToExperiment(UserExperimentsDTO $userExperimentDTO): ExperimentBranchUser
    {
        $experiment = $this
            ->idResolver
            ->getExperimentsByResolvedId(
                $userExperimentDTO->getExperimentId(),
                $userExperimentDTO->getOwner()
            );

        /**
         * @var ExperimentBranches $experimentBranch
         */
        $experimentBranch = $experiment
            ->branches()
            ->where(
                'id',
                (new EntityEncoder())->decode($userExperimentDTO->getExperimentBranchId(), 'experiment_branches')
            )
            ->firstOrFail();

        $experimentUser = $this
            ->experimentUsersRepository
            ->getExperimentsByUserSignatureAndOwner(
                $userExperimentDTO->getUserSignature(),
                $userExperimentDTO->getOwner()
            );

        if (empty($experimentUser)) {
            $experimentUser = $this
                ->experimentUsersRepository
                ->createExperimentUser(
                    $userExperimentDTO->getOwner(),
                    $userExperimentDTO->getUserSignature(),
                );
        }

        $experimentBranchUser = $this
            ->branchUserRepository
            ->getExperimentBranchUserByExperimentIdAndExperimentUserId(
                $experiment->id,
                $experimentUser->id
            );

        if ($experimentBranchUser && $userExperimentDTO->isForce()) {
            $experimentBranchUser->delete();
        }

        if (!$experimentBranchUser) {
            $experimentBranchUser = $this
                ->branchUserManager
                ->createExperimentBranchUser(
                    $experiment->id,
                    $experimentBranch->id,
                    $experimentUser->id
                );
        }

        return $experimentBranchUser;
    }

    public function deleteUserFromExperiment(UserExperimentsDTO $userExperimentDTO):void
    {
        $experimentId = (new EntityEncoder())
            ->decode($userExperimentDTO->getExperimentId(), 'experiments');

        $branchId = (new EntityEncoder())
            ->decode($userExperimentDTO->getExperimentBranchId(), 'experiment_branches');

        $usersId = (new ExperimentUsers())
            ->newQuery()
            ->select('id')
            ->where('user_signature', $userExperimentDTO->getUserSignature())
            ->where('owner_id', $userExperimentDTO->getOwner())
            ->get();

        if ($usersId->isEmpty()) {
            throw new ModelNotFoundException('Failed to find an user');;
        }

        (new ExperimentBranchUser())
            ->newQuery()
            ->where('experiment_user_id', $usersId[0]->id)
            ->where('experiment_branch_id', $branchId)
            ->where('experiment_id', $experimentId)
            ->delete();
    }
}
