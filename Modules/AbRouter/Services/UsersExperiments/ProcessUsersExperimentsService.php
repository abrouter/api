<?php
declare(strict_types =1);

namespace Modules\AbRouter\Services\UsersExperiments;

use Modules\AbRouter\Managers\AllUsersExperimentsHotStorageManager;
use Modules\AbRouter\Repositories\Experiments\ExperimentUsersRepository;
use Modules\AbRouter\Transformers\AllUsersExperimentsTransformer;

class ProcessUsersExperimentsService
{
    private ExperimentUsersRepository $experimentUsersRepository;

    private AllUsersExperimentsTransformer $allUsersExperimentsTransformer;

    private AllUsersExperimentsHotStorageManager $allUsersExperimentsHotStorageManager;

    public function __construct(
        ExperimentUsersRepository $experimentUsersRepository,
        AllUsersExperimentsTransformer $allUsersExperimentsTransformer,
        AllUsersExperimentsHotStorageManager $allUsersExperimentsHotStorageManager
    ) {
        $this->experimentUsersRepository = $experimentUsersRepository;
        $this->allUsersExperimentsTransformer = $allUsersExperimentsTransformer;
        $this->allUsersExperimentsHotStorageManager = $allUsersExperimentsHotStorageManager;
    }

    public function processByOwnerId(int $ownerId): void
    {
        $allUsersExperiments = $this->allUsersExperimentsTransformer->getAllUsersExperiments(
            $this->experimentUsersRepository->getAllUsersExperiments($ownerId)
        );
        $this->allUsersExperimentsHotStorageManager->store($ownerId, collect($allUsersExperiments));
    }
}
