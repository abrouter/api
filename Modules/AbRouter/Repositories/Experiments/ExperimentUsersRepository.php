<?php
declare(strict_types=1);

namespace Modules\AbRouter\Repositories\Experiments;

use Modules\Core\Repositories\BaseRepository;
use Modules\AbRouter\Models\Experiments\ExperimentUsers;

class ExperimentUsersRepository extends BaseRepository
{
    public function getExperimentsByUserSignatureAndOwner(string $userSignature, int $owner): ExperimentUsers
    {
        /**
         * @var ExperimentUsers $model
         */
        $model = $this
            ->query()
            ->where(function ($query) use($userSignature, $owner) {
                $query->where('owner_id', $owner)
                    ->where('user_signature', $userSignature);
            })
            ->firstOrFail();

        return $model;
    }

    public function getModel(): ExperimentUsers
    {
        return new ExperimentUsers();
    }
}
