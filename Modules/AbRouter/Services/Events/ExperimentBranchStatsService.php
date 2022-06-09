<?php
declare(strict_types=1);

namespace Modules\AbRouter\Services\Events;

use Illuminate\Support\Collection;
use Modules\AbRouter\Repositories\Events\EventsRepository;
use Modules\AbRouter\Repositories\Events\UserEventsRepository;
use Modules\AbRouter\Repositories\RelatedUser\RelatedUserRepository;
use Modules\AbRouter\Repositories\Experiments\ExperimentBranchUserRepository;
use Modules\AbRouter\Services\Events\DTO\StatsQueryDTO;
use Modules\AbRouter\Services\Events\DTO\StatsResultsDTO;

class ExperimentBranchStatsService extends SimpleStatsService
{
    function __construct(
        UserEventsRepository           $userEventsRepository,
        EventsRepository               $eventsRepository,
        RelatedUserRepository          $relatedUserRepository,
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
        $date = $this->convertDateTime($statsQueryDTO->getDateFrom(), $statsQueryDTO->getDateTo());

        $allUserEvents = $this->userEventsRepository->getWithOwnerByTagAndDate(
            $statsQueryDTO->getOwnerId(),
            $statsQueryDTO->getTag(),
            $date['date_from'],
            $date['date_to']
        );

        $allRelatedUsers = $allUserEvents
            ->load('relatedUsers')
            ->pluck('relatedUsers')
            ->flatten();

        $eventsNames = $this->getDisplayEvents($statsQueryDTO->getOwnerId());

        $uniqUsersIds = $this->getUniqUsersIds($allUserEvents);
        $uniqRelatedUsersIds = $this->getUniqRelatedUsersIds(...$allRelatedUsers->all());
        $uniqUsers = $this->getFinalUniqUsers($uniqUsersIds, $uniqRelatedUsersIds);
        $jointUsers = $this->getJointUsersFromEventsAndExperimentBranch(
            $statsQueryDTO->getExperimentBranchId(),
            $uniqUsers
        );
        $uniqUsersCount = count($jointUsers);

        $eventCounters = $this->getCounters(
            $allUserEvents,
            $jointUsers,
            'event'
        );

        $eventPercentages = $this->getPercentages(
            $eventsNames,
            $eventCounters,
            $uniqUsersCount
        );

        return new StatsResultsDTO(
            $eventPercentages,
            $eventCounters,
            [],
            [],
            []
        );
    }

    private function getExperimentUsersIdByExperimentBranchId(int $branchId): Collection
    {
        return $this->experimentBranchUserRepository->getUsersIdByBranchId($branchId);
    }

    private function getJointUsersFromEventsAndExperimentBranch(int $branchId, array $uniqUsers): array
    {
        $usersSignatures = $this
            ->getExperimentUsersIdByExperimentBranchId($branchId)
            ->load('experimentUser')
            ->pluck('experimentUser')
            ->flatten()
            ->pluck('user_signature')
            ->unique()
            ->flip();

        return array_filter($uniqUsers, function(string $item) use ($usersSignatures): bool {
            return $usersSignatures->has($item);
        });
    }
}
