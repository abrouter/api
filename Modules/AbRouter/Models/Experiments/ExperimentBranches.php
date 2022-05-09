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
 * @property int user_id
 * @property string name
 * @property string uid
 * @property int percent
 * @property string config
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Experiment experiment
 */
class ExperimentBranches extends Model
{
    use IdTrait;

    protected $casts = [
        'id' => 'int',
        'experiment_id' => 'int',
        'name' => 'string',
        'uid' => 'string',
        'config' => 'string',
        'percent' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'experiment_id',
        'name',
        'uid',
        'config',
        'percent',
    ];

    public static function getType(): string
    {
        return 'experiment_branches';
    }

    public function experiment(): HasOne
    {
        return $this->hasOne(Experiment::class, 'id', 'experiment_id');
    }
}
