<?php
declare(strict_types =1);

namespace Modules\AbRouter\Services\UsersExperiments;

use App\Models\User;
use Modules\AbRouter\Repositories\Users\UsersRepository;

class ProcessAllUsersExperimentsService
{
    private ProcessUsersExperimentsService $processUsersExperimentsService;

    private UsersRepository $usersRepository;

    public function __construct(
        ProcessUsersExperimentsService $processUsersExperimentsService,
        UsersRepository $usersRepository
    ) {
        $this->processUsersExperimentsService = $processUsersExperimentsService;
        $this->usersRepository = $usersRepository;
    }

    public function processAll(): void
    {
        $allUsers = $this->usersRepository->getUsersWithExperimentExists();
        $allUsers->each(function (User $user) {
            $this->processUsersExperimentsService->processByOwnerId($user->id);
        });
    }
}
