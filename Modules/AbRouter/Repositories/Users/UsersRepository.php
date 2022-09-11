<?php
declare(strict_types=1);

namespace Modules\AbRouter\Repositories\Users;

use App\Models\User;
use Illuminate\Support\Collection;
use Modules\Core\Repositories\BaseRepository;

class UsersRepository extends BaseRepository
{
    public function getUsersWithExperimentExists(): Collection
    {
        return $this
            ->query()
            ->select('users.*')
            ->join('experiments', 'users.id', '=', 'experiments.owner_id')
            ->whereNotNull('experiments.id')
            ->get();
    }

    protected function getModel()
    {
        return new User();
    }
}
