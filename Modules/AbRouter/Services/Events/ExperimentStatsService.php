<?php
declare(strict_types =1);

namespace Modules\AbRouter\Services\Events;

use Illuminate\Support\Collection;
use Modules\AbRouter\Repositories\Events\EventsRepository;
use Modules\AbRouter\Repositories\Events\UserEventsRepository;
use Modules\AbRouter\Repositories\RelatedUser\RelatedUserRepository;
use Modules\AbRouter\Repositories\Experiments\ExperimentBranchUserRepository;
use Modules\AbRouter\Repositories\Experiments\ExperimentsRepository;
use Modules\AbRouter\Services\Events\DTO\StatsQueryDTO;
use Modules\AbRouter\Services\Events\DTO\StatsResultsDTO;
use Modules\AbRouter\Models\Experiments\Experiment;
use Modules\Core\EntityId\Encoder;

class ExperimentStatsService extends SimpleStatsService
{
    function __construct(
        UserEventsRepository $userEventsRepository,
        EventsRepository $eventsRepository,
        RelatedUserRepository $relatedUserRepository,
        ExperimentBranchUserRepository $experimentBranchUserRepository,
        ExperimentsRepository $experimentsRepository
    )
    {
        parent::__construct(
            $userEventsRepository,
            $eventsRepository,
            $relatedUserRepository
        );

        $this->experimentBranchUserRepository = $experimentBranchUserRepository;
        $this->experimentsRepository = $experimentsRepository;
    }

    public function getStatsByExperiment(StatsQueryDTO $statsQueryDTO): StatsResultsDTO
    {
        if(!empty($statsQueryDTO->getDateFrom() && $statsQueryDTO->getDateTo())) {
            $date = $this->convertDateTime($statsQueryDTO->getDateFrom(), $statsQueryDTO->getDateTo()) ;
        }

        $allUserEvents = $this->userEventsRepository->getWithOwnerByTagAndDate(
            $statsQueryDTO->getOwnerId(),
            $statsQueryDTO->getTag(),
            $date['date_from'] ?? null,
            $date['date_to'] ?? null
        );
        $allUserEvents->load('relatedUsers');
        $allRelatedUsers = $allUserEvents->pluck('relatedUsers')->flatten();
        
        $eventsNames = $this->getDisplayEvents($statsQueryDTO->getOwnerId());
        
        $uniqUsersIds = $this->getUniqUsersIds($allUserEvents);
        $uniqRelatedUsersIds = $this->getUniqRelatedUsersIds($uniqUsersIds, $allRelatedUsers->all());
        $uniqUsers  = $this->getFinalUniqUsers($uniqUsersIds, $uniqRelatedUsersIds);
        $jointUsers = $this->getJointUsersFromEventsAndExperiment(
            $statsQueryDTO->getOwnerId(), 
            $statsQueryDTO->getExperimentId(), 
            $uniqUsers
        );
        
        $eventCounters = [];
        $eventPercentages = [];

        foreach($jointUsers as $key => $jointUser) {
            $uniqUsersCount = count($jointUser);
        
            $eventCounters[$key] = $this->getEventCounters(
                $allUserEvents,
                $jointUser
            );
            
            $eventPercentages[$key] = $this->getEventsPercentages(
                $eventsNames,
                $eventCounters[$key],
                $uniqUsersCount
            );
        }
        
        return new StatsResultsDTO($eventPercentages, $eventCounters);
    }

    private function getExperimentUsersIdByExperimentBranchId(int $experimentId): Collection
    {
        $usersIdCollection = $this->experimentBranchUserRepository->getUsersIdByExperimentId($experimentId);

        return $usersIdCollection;
    }

    private function getExperimentIdById(string $experimentId , int $owner): Experiment
    {
        $experiment = $this->experimentsRepository->getExperimentsById($experimentId, $owner);

        return $experiment;
    }

    private function getExperimentByAlias(string $alias, int $owner): Experiment
    {
        $experiment = $this->experimentsRepository->getExperimentsByAlias($alias, $owner);

        return $experiment;
    }

    private function getJointUsersFromEventsAndExperiment(
        int $owner, 
        string $experimentId, 
        array $uniqUsers
    ): array {
        $jointUsers = [];
        $usersSignatures = [];
        $checkId = preg_match('/^([A-Z0-9]{8})(-){1}([A-Z0-9]{4})(-){1}([A-Z0-9]{4})(-){1}([A-Z0-9]{8})$/', $experimentId);
        
        if($checkId) {
            $experiment = $this->getExperimentIdById($experimentId, $owner);
        } else $experiment = $this->getExperimentByAlias($experimentId, $owner);
        
        if(isset($experiment)) {
            $usersId = $this->getExperimentUsersIdByExperimentBranchId($experiment->id);
        }

        $usersId->load('experimentBranch', 'experimentUser');
        $branchesNames = $usersId->pluck('experimentBranch')->flatten()->pluck('name', 'id')->toArray();

        foreach($branchesNames as $id => $branchName) {
            foreach($usersId as $userId) {
                if($userId->experiment_branch_id === $id) {
                    $usersSignatures[] = $userId->experimentUser->user_signature;
                }
            }
            
            $uniqUsersSignatures = array_flip(array_unique($usersSignatures));
            unset($usersSignatures);

            foreach($uniqUsers as $uniqUser) {
                if(isset($uniqUsersSignatures[$uniqUser])) {
                    $jointUsers[$branchName][] = $uniqUser;
                }
            }
        }

        return $jointUsers;
    }
}
