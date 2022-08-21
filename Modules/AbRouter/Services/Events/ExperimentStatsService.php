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
use Modules\AbRouter\Services\Events\Stats\StatsFactory;

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
        ExperimentsRepository $experimentsRepository,
        StatsFactory $statsFactory
    ) {
        parent::__construct(
            $userEventsRepository,
            $eventsRepository,
            $relatedUserRepository,
            $statsFactory
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
                $statsQueryDTO->getTag(),
                $date['date_from'],
                $date['date_to']
            )
            ->load('relatedUsers');;

        $totalUsers = $this->getTotalUsers(
            $statsQueryDTO->getOwnerId(),
            $statsQueryDTO->getExperimentId()
        );

        $allRelatedUsers = $allUserEvents
            ->pluck('relatedUsers')
            ->flatten();

        $allDisplayEvents = $this->getDisplayEvents($statsQueryDTO->getOwnerId());
        $displayEventsWithTypeSummarizable = $this->getDisplayEventsWithTypeSummarizable($allDisplayEvents);
        $displayEventsWithTypeIncremental = $this->getDisplayEventsWithTypeIncremental($allDisplayEvents);
        $displayEventsWithTypeIncrementalUnique = $this->getDisplayEventsWithTypeIncrementalUnique($allDisplayEvents);
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

        $incrementalCounters = [];
        $incrementalUniqueCounters = [];
        $eventPercentages = [];
        $summarizationCounters = [];

        foreach($jointUsers as $key => $jointUser) {
            $incrementalCounters[$key] = $this
                ->statsFactory
                ->getStatsMethod('event')
                ->getCounters(
                    $allUserEvents,
                    $jointUser,
                    $displayEventsWithTypeIncremental
                );

            $incrementalUniqueCounters[$key] = $this
                ->statsFactory
                ->getStatsMethod('event-unique')
                ->getCounters(
                    $allUserEvents,
                    $jointUser,
                    $displayEventsWithTypeIncrementalUnique
                );

            $summarizationCounters[$key] = $this
                ->statsFactory
                ->getStatsMethod('revenue')
                ->getCounters(
                    $allUserEvents,
                    [],
                    $displayEventsWithTypeSummarizable
                );

            $eventPercentages[$key] = $this
                ->statsFactory
                ->getStatsMethod('event-unique')
                ->getPercentages(
                    $allDisplayEvents,
                    $incrementalUniqueCounters[$key],
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
            $incrementalCounters,
            $incrementalUniqueCounters,
            $summarizationCounters,
            [],
            [],
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
        $owner,
        $experimentId
    ): int {
        $allUserEvents = $this
            ->userEventsRepository
            ->getWithOwnerByTagAndDate(
                $owner,
                null
            );

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
