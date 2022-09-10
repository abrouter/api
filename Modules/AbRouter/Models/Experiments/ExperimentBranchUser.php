<?php
declare(strict_types=1);

namespace Modules\AbRouter\Models\Experiments;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Core\EntityId\IdTrait;

/**
 * Class User
 * @package Modules\Auth\Models\User
 * @property int id
 * @property int owner_id
 * @property int experiment_user_id
 * @property int experiment_id
 * @property int experiment_branch_id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property ExperimentUsers experimentUser
 * @property Experiment experiment
 * @property ExperimentBranches experimentBranch
 */
class ExperimentBranchUser extends Model
{
    use IdTrait;

    protected $table = 'experiment_user_branches';

    protected $casts = [
        'id' => 'int',
        'owner_id' => 'int',
        'experiment_user_id' => 'int',
        'experiment_id' => 'int',
        'experiment_branch_id' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'owner_id',
        'experiment_user_id',
        'experiment_id',
        'experiment_branch_id',
    ];

    public static function getType(): string
    {
        return 'experiment_branch_users';
    }

    public function experimentUser(): HasOne
    {
        return $this->hasOne(ExperimentUsers::class, 'id', 'experiment_user_id');
    }

    public function experiment(): HasOne
    {
        return $this->hasOne(Experiment::class, 'id', 'experiment_id');
    }

    public function experimentBranch(): HasOne
    {
        return $this->hasOne(ExperimentBranches::class, 'id', 'experiment_branch_id');
    }
}
