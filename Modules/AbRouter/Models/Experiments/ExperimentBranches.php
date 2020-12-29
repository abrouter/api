<?php
declare(strict_types=1);

namespace Modules\AbRouter\Models\Experiments;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * @package Modules\Auth\Models\User
 * @property int id
 * @property int user_id
 * @property string name
 * @property string config
 * @property boolean is_enabled
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class ExperimentBranches extends Model
{
    protected $casts = [
        'id' => 'int',
        'experiment_id' => 'int',
        'name' => 'string',
        'config' => 'string',
        'percent' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'experiment_id',
        'name',
        'config',
        'percent',
    ];

    public static function getType(): string
    {
        return 'experiment_branches';
    }
}
