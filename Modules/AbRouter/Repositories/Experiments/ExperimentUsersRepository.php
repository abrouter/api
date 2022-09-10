<?php
declare(strict_types=1);

namespace Modules\AbRouter\Repositories\Experiments;

use Illuminate\Support\Collection;
use Modules\AbRouter\Models\Experiments\Experiment;
use Modules\AbRouter\Models\Experiments\ExperimentBranchUser;
use Modules\Core\Repositories\BaseRepository;
use Modules\AbRouter\Models\Experiments\ExperimentUsers;

class ExperimentUsersRepository extends BaseRepository
{
    /**
     * @param string $userSignature
     * @param int $owner
     * @return ExperimentUsers|null
     */
    public function getExperimentsByUserSignatureAndOwner(string $userSignature, int $owner): ?ExperimentUsers
    {
        /**
         * @var ExperimentUsers $model
         */
        $model = $this
            ->query()
            ->where('owner_id', $owner)
            ->where('user_signature', $userSignature)
            ->first();

        return $model;
    }

    /**
     * @param array $userSignatures
     * @param int $owner
     * @return array
     */
    public function getExperimentUserIdsByUserSignatureAndOwner(array $userSignatures, int $owner): array
    {
        $ids = $this
            ->query()
            ->select(['id'])
            ->where('owner_id', $owner)
            ->whereIn('user_signature', $userSignatures)
            ->get()
            ->toArray();

        return $ids;
    }

    /**
     * @param int $owner
     * @param string $userSignature
     * @return ExperimentUsers
     */
    public function createExperimentUser(int $owner, string $userSignature): ExperimentUsers
    {
        /**
         * @var ExperimentUsers $model
         */
        $model = ExperimentUsers
            ::query()
            ->create([
                'owner_id' => $owner,
                'user_signature' => $userSignature,
                'config' => '{}',
            ]);

        return $model;
    }

    /**
     * @param int $ownerId
     * @return Collection
     */
    public function getAllUsersExperiments(int $ownerId): Collection
    {
        /**
         * @var Collection $collection
         */

        $collection = $this
            ->query()
            ->where('owner_id', $ownerId)
            ->get();

        $allExperimentBranchesById = ExperimentBranchUser::query()
            ->where('owner_id', $ownerId)
            ->get()
            ->reduce(function ($acc, ExperimentBranchUser $experimentBranchUser) {
                $acc[$experimentBranchUser->id] = $experimentBranchUser;
                return $acc;
            });

        $allExperimentBranchesByExperimentUserId = ExperimentBranchUser::query()
            ->where('owner_id', $ownerId)
            ->get()
            ->reduce(function ($acc, ExperimentBranchUser $experimentBranchUser) {
                $acc[$experimentBranchUser->experiment_user_id][] = $experimentBranchUser;
                return $acc;
            });


        $allExperiments = Experiment::query()
            ->where('owner_id', $ownerId)
            ->get()
            ->reduce(function ($acc, Experiment $experiment) {
                $acc[$experiment->id] = $experiment;
                return $acc;
            });


        foreach ($collection as $key => $item) {
            /**
             * @var ExperimentUsers $item
             */
            if (empty($allExperimentBranchesByExperimentUserId[$item->id])) {
                continue;
            }

            $experimentUserBranches = $allExperimentBranchesByExperimentUserId[$item->id];
            $item->experimentUser = $experimentUserBranches;
            $item->experimentBranchUsers = $item->experimentUser;


            foreach ($item->experimentUser as $experimentBranchUser) {

                if (empty($experimentBranchUser)) {
                    continue;
                }

                if (empty($allExperimentBranchesById[$experimentBranchUser->experiment_branch_id])) {
                    continue;
                }

                /**
                 * @var ExperimentBranchUser $experimentBranchUser
                 */
                $experimentBranchUser->experiment = $allExperiments[$experimentBranchUser->experiment_id];
                $experimentBranchUser->experimentBranch = $allExperimentBranchesById[$experimentBranchUser->experiment_branch_id];
            }

            $item->experimentUser = collect($experimentUserBranches);
            $item->experimentBranchUsers = $item->experimentUser;
            $collection[$key] = $item;
        }

        return collect($collection);
    }

    public function getModel(): ExperimentUsers
    {
        return new ExperimentUsers();
    }
}
