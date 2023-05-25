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
use Modules\AbRouter\Services\Experiment\ExperimentIdResolver;

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

    /**
     * @var ExperimentIdResolver
     */
    private $experimentIdResolver;

    function __construct(
        UserEventsRepository $userEventsRepository,
        EventsRepository $eventsRepository,
        RelatedUserRepository $relatedUserRepository,
        ExperimentBranchUserRepository $experimentBranchUserRepository,
        ExperimentsRepository $experimentsRepository,
        StatsFactory $statsFactory,
        ExperimentIdResolver $experimentIdResolver
    ) {
        parent::__construct(
            $userEventsRepository,
            $eventsRepository,
            $relatedUserRepository,
            $statsFactory
        );

        $this->experimentBranchUserRepository = $experimentBranchUserRepository;
        $this->experimentsRepository = $experimentsRepository;
        $this->experimentIdResolver = $experimentIdResolver;
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
            ->load('relatedUsers');

        $totalUsers = $this->getTotalUsers(
            $statsQueryDTO->getOwnerId(),
            $statsQueryDTO->getExperimentId(),
            $date['date_from'],
            $date['date_to'],
            $allUserEvents
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

        $allUsersSum = array_map('count', $jointUsers);
        $allUsersSum = array_sum($allUsersSum);

        foreach($jointUsers as $branchName => $jointUser) {
            $incrementalCounters[$branchName] = $this
                ->statsFactory
                ->getStatsMethod('event')
                ->getCounters(
                    $allUserEvents,
                    $jointUser,
                    $displayEventsWithTypeIncremental
                );

            $incrementalUniqueCounters[$branchName] = $this
                ->statsFactory
                ->getStatsMethod('event-unique')
                ->getCounters(
                    $allUserEvents,
                    $jointUser,
                    $displayEventsWithTypeIncrementalUnique
                );

            $summarizationCounters[$branchName] = $this
                ->statsFactory
                ->getStatsMethod('revenue')
                ->getCounters(
                    $allUserEvents,
                    [],
                    $displayEventsWithTypeSummarizable
                );



            $percentageCounter = empty($incrementalUniqueCounters[$branchName]) ? $incrementalCounters
                : $incrementalUniqueCounters;

            $eventPercentages[$branchName] = $this
                ->statsFactory
                ->getStatsMethod(empty($incrementalUniqueCounters) ? 'event' : 'event-unique')
                ->getPercentages(
                    $allDisplayEvents,
                    $percentageCounter[$branchName],
                    $allUsersSum
                );
        }

        /**
         * Hotfix for frontend
         */
        foreach ($incrementalCounters as $incrementalCounterKey => $incrementalCounterValue) {
            $incrementalUniqueCounters[$incrementalCounterKey] = $incrementalCounterValue;
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
        return $this->experimentIdResolver->getExperimentsByResolvedId($experimentId, $owner);
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
        $experimentId,
        string $dateFrom,
        string $dateTo,
        ?Collection $allUserEvents = null
    ): int {
        if ($allUserEvents === null) {
            $allUserEvents = $this
                ->userEventsRepository
                ->getWithOwnerByTagAndDate(
                    $owner,
                    null,
                    $dateFrom,
                    $dateTo
                )
                ->load('relatedUsers');
        }

        $allRelatedUsers = $allUserEvents
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
        //using too much memory here
        $jointUsers = $this->getJointUsersFromEventsAndExperiment($experiment, $uniqUsers);
        $totalUsers = [];

        foreach ($jointUsers as $branchName => $userSignatures) {
            $totalUsers = array_merge($totalUsers, $userSignatures);
        }

        $uniqTotalUsers = array_unique($totalUsers);

        return count($uniqTotalUsers);
    }
}
