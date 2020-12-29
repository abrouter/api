<?php
declare(strict_types=1);

namespace Modules\AbRouter\Models\Experiments;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * @package Modules\Auth\Models\User
 * @property int id
 * @property int experiment_user_id
 * @property int experiment_id
 * @property int experiment_branch_id
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class ExperimentBranchUsers extends Model
{
    protected $casts = [
        'id' => 'int',
        'experiment_user_id' => 'int',
        'experiment_id' => 'int',
        'experiment_branch_id' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'experiment_user_id',
        'experiment_id',
        'experiment_branch_id',
    ];

    public static function getType(): string
    {
        return 'experiment_branch_users';
    }
}
