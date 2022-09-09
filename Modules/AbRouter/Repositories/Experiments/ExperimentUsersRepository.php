<?php
declare(strict_types=1);

namespace Modules\AbRouter\Repositories\Experiments;

use Illuminate\Database\Eloquent\Collection;
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
            ->with(
                ['experimentUser.experiment', 'experimentUser.experimentBranch']
            )
            ->get();

        return $collection;
    }

    public function getModel(): ExperimentUsers
    {
        return new ExperimentUsers();
    }
}
