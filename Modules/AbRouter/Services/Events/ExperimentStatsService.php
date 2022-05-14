<?php
declare(strict_types =1);

namespace Modules\AbRouter\Services\Events;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Modules\AbRouter\Repositories\Events\EventsRepository;
use Modules\AbRouter\Repositories\Events\UserEventsRepository;
use Modules\AbRouter\Repositories\RelatedUser\RelatedUserRepository;
use Modules\AbRouter\Repositories\Experiments\ExperimentBranchUserRepository;
use Modules\AbRouter\Repositories\Experiments\ExperimentsRepository;
use Modules\AbRouter\Services\Events\DTO\StatsQueryDTO;
use Modules\AbRouter\Services\Events\DTO\StatsResultsDTO;
use Modules\AbRouter\Models\Experiments\Experiment;

class ExperimentStatsService extends SimpleStatsService
{
    /**
     * @var ExperimentBranchUserRepository
     */
    private $experimentBranchUserRepository;

    /**
     * @var ExperimentsRepository
     */
    private $experimentsRepository;

    function __construct(
        UserEventsRepository $userEventsRepository,
        EventsRepository $eventsRepository,
        RelatedUserRepository $relatedUserRepository,
        ExperimentBranchUserRepository $experimentBranchUserRepository,
        ExperimentsRepository $experimentsRepository
    ) {
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
        $date = $this->convertDateTime(
            $statsQueryDTO->getDateFrom(),
            $statsQueryDTO->getDateTo()
        );

        $allUserEvents = $this
            ->userEventsRepository
            ->getWithOwnerByTagAndDate(
                $statsQueryDTO->getOwnerId(),
                $statsQueryDTO->getTag()
            );

        $totalUsers = $this->getTotalUsers(
            $allUserEvents,
            $statsQueryDTO->getOwnerId(),
            $statsQueryDTO->getExperimentId()
        );

        $allUserEvents = $allUserEvents
            ->whereBetween(
                'created_at',
                [
                    $date['date_from'],
                    $date['date_to']
                ]
            )
            ->load('relatedUsers');

        $allRelatedUsers = $allUserEvents
            ->pluck('relatedUsers')
            ->flatten();

        $eventsNames = $this->getDisplayEvents($statsQueryDTO->getOwnerId());
        $uniqUsersIds = $this->getUniqUsersIds($allUserEvents);
        $uniqRelatedUsersIds = $this
            ->getUniqRelatedUsersIds(
                ...$allRelatedUsers->all()
            );

        $uniqUsers = $this->getFinalUniqUsers($uniqUsersIds, $uniqRelatedUsersIds);
        $experiment = $this->getExperiment(
            $statsQueryDTO->getOwnerId(),
            $statsQueryDTO->getExperimentId()
        );

        $jointUsers = $this->getJointUsersFromEventsAndExperiment($experiment, $uniqUsers);
        
        $eventCounters = [];
        $eventPercentages = [];
        $eventCountersWithDate = [];

        foreach($jointUsers as $key => $jointUser) {
            $eventCounters[$key] = $this->getCounters(
                $allUserEvents,
                $jointUser,
                'event'
            );

            $eventCountersWithDate[$key] = $this->getCounters(
                $allUserEvents,
                $jointUser,
                'event',
                true
            );
            
            $eventPercentages[$key] = $this->getPercentages(
                $eventsNames,
                $eventCounters[$key],
                count($jointUser)
            );
        }

        $interval = (new \DateTime())
            ->diff(
                $experiment->start_experiment_day
                ?? $experiment->updated_at
            )
            ->days;

        $experiment->days_running = $interval;
        $experiment->total_users = $totalUsers;

        return new StatsResultsDTO(
            $eventPercentages,
            $eventCounters,
            [],
            [],
            $eventCountersWithDate,
            $experiment
        );
    }

    private function getExperimentUsersIdByExperimentId(int $experimentId): Collection
    {
        return $this->experimentBranchUserRepository->getUsersIdByExperimentId($experimentId);
    }

    private function getExperimentIdById(string $experimentId , int $owner): Experiment
    {
        return $this->experimentsRepository->getExperimentsById($experimentId, $owner);
    }

    private function getExperimentByAlias(string $alias, int $owner): Experiment
    {
        return $this->experimentsRepository->getExperimentsByAlias($alias, $owner);
    }

    private function getExperiment(
        int $owner,
        string $experimentId
    ): Experiment {
        $checkId = preg_match(
            '/^([A-Z0-9]{8})(-){1}([A-Z0-9]{4})(-){1}([A-Z0-9]{4})(-){1}([A-Z0-9]{8})$/',
            $experimentId
        );

        $checkId === 1
        ? $experiment = $this->getExperimentIdById($experimentId, $owner)
        : $experiment = $this->getExperimentByAlias($experimentId, $owner);

        return $experiment;
    }

    private function getJointUsersFromEventsAndExperiment(
        Experiment $experiment,
        array $uniqUsers
    ): array {
        $jointUsers = [];

        $usersId = $this
            ->getExperimentUsersIdByExperimentId($experiment->id)
            ->load('experimentBranch', 'experimentUser');

        $branchesNames = $usersId
            ->pluck('experimentBranch')
            ->flatten()
            ->pluck('name', 'id')
            ->toArray();

        foreach($branchesNames as $id => $branchName) {
            $usersSignatures = [];

            foreach($usersId as $userId) {
                if($userId->experiment_branch_id === $id) {
                    $usersSignatures[] = $userId->experimentUser->user_signature;
                }
            }
            
            $uniqUsersSignatures = array_flip(array_unique($usersSignatures));

            foreach($uniqUsers as $uniqUser) {
                if(isset($uniqUsersSignatures[$uniqUser])) {
                    $jointUsers[$branchName][] = $uniqUser;
                }
            }
        }

        return $jointUsers;
    }

    private function getTotalUsers(
        Collection $allUserEvents,
        $owner,
        $experimentId
    ): int {
        $allRelatedUsers = $allUserEvents
            ->load('relatedUsers')
            ->pluck('relatedUsers')
            ->flatten();

        $uniqUsersIds = $this->getUniqUsersIds($allUserEvents);
        $uniqRelatedUsersIds = $this->getUniqRelatedUsersIds(
            ...$allRelatedUsers->all()
        );
        $experiment = $this->getExperiment(
            $owner,
            $experimentId
        );
        $uniqUsers = $this->getFinalUniqUsers($uniqUsersIds, $uniqRelatedUsersIds);
        $jointUsers = $this->getJointUsersFromEventsAndExperiment($experiment, $uniqUsers);
        $totalUsers = [];

        foreach ($jointUsers as $branchName => $userSignatures) {
            $totalUsers = array_merge($totalUsers, $userSignatures);
        }

        $uniqTotalUsers = array_unique($totalUsers);

        return count($uniqTotalUsers);
    }
}
