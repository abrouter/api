<?php
declare(strict_types=1);

namespace Modules\AbRouter\Services\Experiment;

use Modules\AbRouter\Models\Experiments\ExperimentBranches;
use Modules\AbRouter\Models\Experiments\ExperimentBranchUser;
use Modules\AbRouter\Repositories\Experiments\ExperimentBranchUserRepository;
use Modules\AbRouter\Repositories\Experiments\ExperimentUsersRepository;
use Modules\AbRouter\Managers\Experiments\ExperimentBranchUserManager;
use Modules\AbRouter\Services\Experiment\DTO\AddUserToExperimentDTO;
use Modules\Core\EntityId\EntityEncoder;

class AddUserToExperimentService
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
     * @param AddUserToExperimentDTO $addUserToExperimentDTO
     * @return ExperimentBranchUser
     * @throws \Exception
     */
    public function addUserToExperiment(AddUserToExperimentDTO $addUserToExperimentDTO): ExperimentBranchUser
    {
        $experiment = $this
            ->idResolver
            ->getExperimentsByResolvedId(
                $addUserToExperimentDTO->getExperimentId(),
                $addUserToExperimentDTO->getOwner()
            );

        /**
         * @var ExperimentBranches $experimentBranch
         */
        $experimentBranch = $experiment
            ->branches()
            ->where(
                'id',
                (new EntityEncoder())->decode($addUserToExperimentDTO->getExperimentBranchId(), 'experiment_branches')
            )
            ->firstOrFail();

        $experimentUser = $this
            ->experimentUsersRepository
            ->getExperimentsByUserSignatureAndOwner(
                $addUserToExperimentDTO->getUserSignature(),
                $addUserToExperimentDTO->getOwner()
            );

        if (empty($experimentUser)) {
            $experimentUser = $this
                ->experimentUsersRepository
                ->createExperimentUser(
                    $addUserToExperimentDTO->getOwner(),
                    $addUserToExperimentDTO->getUserSignature(),
                );
        }

        $experimentBranchUser = $this
            ->branchUserRepository
            ->getExperimentBranchUserByExperimentIdAndExperimentUserId(
                $experiment->id,
                $experimentUser->id
            );

        if (empty($experimentBranchUser)) {
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
}
