<?php
declare(strict_types=1);

namespace Modules\AbRouter\Repositories\Experiments;

use Illuminate\Database\Eloquent\Collection;
use Modules\AbRouter\Models\Experiments\ExperimentBranchUser;
use Modules\Core\Repositories\BaseRepository;

class ExperimentBranchUserRepository extends BaseRepository
{
    public function getUsersIdByBranchId(int $branchId): Collection
    {
        /**
         * @var Collection $collection
         */
        return $this->query()
            ->where('experiment_branch_id', $branchId)
            ->get();
    }

    public function getUsersIdByExperimentId(int $experimentId, string $dateFrom, string $dateTo): Collection
    {
        /**
         * @var Collection $collection
         */
        $query = $this->query()
            ->where('experiment_id', $experimentId)
            ->whereBetween('created_at', [$dateFrom, $dateTo]);

        $collection = $query->get();
        return $collection;
    }

    public function getExperimentBranchUserByExperimentIdAndExperimentUserId(int $experimentId, int $experimentUserId)
    {
        /**
         * @var ExperimentBranchUser
         */
        return $this
            ->query()
            ->where(function ($query) use ($experimentId, $experimentUserId) {
                $query->where('experiment_id', $experimentId)
                    ->where('experiment_user_id', $experimentUserId);
            })
            ->first();
    }

    /**
     * @param int $ownerId
     * @param string $userSignature
     * @return Collection
     */
    public function getExperimentsBranchesByUserSignature(int $ownerId, string $userSignature): Collection
    {
        /**
         * @var Collection $collection
         */
        $collection = $this->query()
            ->whereHas('experimentUser', function ($query) use ($ownerId, $userSignature) {
                $query->where('user_signature', $userSignature);
                $query->where('owner_id', $ownerId);
            })
        ->get();

        return $collection;
    }

    /**
     * @param string $userSignature
     * @param int $ownerId
     * @return Collection
     */
    public function getExperimentsIdByUserSignatureAndOwner(string $userSignature, int $ownerId): Collection
    {
        /**
         * @var Collection $collection
         */
        $collection = $this
            ->query()
            ->select('experiment_id')
            ->whereHas('experimentUser', function ($query) use ($ownerId, $userSignature) {
                $query->where('user_signature', $userSignature);
                $query->where('owner_id', $ownerId);
            })
            ->get();

        return $collection;
    }

    protected function getModel(): ExperimentBranchUser
    {
        return new ExperimentBranchUser();
    }
}
