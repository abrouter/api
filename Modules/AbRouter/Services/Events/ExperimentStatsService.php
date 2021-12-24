<?php
declare(strict_types =1);

namespace Modules\AbRouter\Services\Events;

use Illuminate\Support\Collection;
use Modules\AbRouter\Repositories\Events\EventsRepository;
use Modules\AbRouter\Repositories\Events\UserEventsRepository;
use Modules\AbRouter\Repositories\RelatedUser\RelatedUserRepository;
use Modules\AbRouter\Repositories\Experiments\ExperimentBranchUserRepository;
use Modules\AbRouter\Services\Events\DTO\StatsQueryDTO;
use Modules\AbRouter\Services\Events\DTO\StatsResultsDTO;

class ExperimentStatsService extends SimpleStatsService
{
    function __construct(
        UserEventsRepository $userEventsRepository,
        EventsRepository $eventsRepository,
        RelatedUserRepository $relatedUserRepository,
        ExperimentBranchUserRepository $experimentBranchUserRepository
    )
    {
        parent::__construct(
            $userEventsRepository,
            $eventsRepository,
            $relatedUserRepository
        );

        $this->experimentBranchUserRepository = $experimentBranchUserRepository;
    }

    public function getStatsByExperimentBranch(StatsQueryDTO $statsQueryDTO): StatsResultsDTO
    {   
        $allUserEvents = $this->userEventsRepository->getWithOwnerByTag(
            $statsQueryDTO->getOwnerId(),
            $statsQueryDTO->getTag()
        );
        $allUserEvents->load('relatedUsers');
        $allRelatedUsers = $allUserEvents->pluck('relatedUsers')->flatten();
        
        $eventsNames = $this->getDisplayEvents($statsQueryDTO->getOwnerId());
        
        $uniqUsersIds = $this->getUniqUsersIds($allUserEvents);
        $uniqRelatedUsersIds = $this->getUniqRelatedUsersIds($uniqUsersIds, $allRelatedUsers->all());
        $uniqUsers  = $this->getFinalUniqUsers($uniqUsersIds, $uniqRelatedUsersIds);
        $jointUsers = $this->getJointUsersFromEventsAndExperiments($statsQueryDTO->getExperimentBranchId(), $uniqUsers);
        $uniqUsersCount = count($jointUsers);
        
        $eventCounters = $this->getEventCounters(
            $allUserEvents,
            $jointUsers
        );
        
        $eventPercentages = $this->getEventsPercentages(
            $eventsNames,
            $eventCounters,
            $uniqUsersCount
        );
        
        return new StatsResultsDTO($eventPercentages, $eventCounters);
    }

    private function getExperimentUsersIdByExperimentBranchId(int $branchId): Collection
    {
        $usersIdCollection = $this->experimentBranchUserRepository->getUsersId($branchId);

        return $usersIdCollection;
    }

    private function getJointUsersFromEventsAndExperiments(int $branchId, $uniqUsers)
    {
        $jointUsers = [];

        $usersId = $this->getExperimentUsersIdByExperimentBranchId($branchId);

        $usersId->load('experimentUser');

        $usersSignatures = $usersId->pluck('experimentUser')->flatten()->pluck('user_signature')->toArray();
        $uniqUsersSignatures = array_flip(array_unique($usersSignatures));

        foreach($uniqUsers as $uniqUser) {
            if(isset($uniqUsersSignatures[$uniqUser])) {
                $jointUsers[] = $uniqUser;
            }
        }

        return $jointUsers;
    }
}
