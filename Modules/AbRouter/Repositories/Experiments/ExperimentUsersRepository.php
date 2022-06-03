<?php
declare(strict_types=1);

namespace Modules\AbRouter\Repositories\Experiments;

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

    public function getModel(): ExperimentUsers
    {
        return new ExperimentUsers();
    }
}
