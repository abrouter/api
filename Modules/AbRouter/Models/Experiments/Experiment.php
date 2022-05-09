<?php
declare(strict_types=1);

namespace Modules\AbRouter\Models\Experiments;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Modules\Auth\Models\User\User;
use Modules\Core\EntityId\IdTrait;

/**
 * Class User
 * @package Modules\Auth\Models\User
 * @property int id
 * @property int owner_id
 * @property string name
 * @property string uid
 * @property string config
 * @property string alias
 * @property boolean is_enabled
 * @property boolean is_feature_toggle
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property User owner
 * @property Collection $branches
 */
class Experiment extends Model
{
    use IdTrait;

    protected $casts = [
        'id' => 'int',
        'owner_id' => 'int',
        'name' => 'string',
        'uid' => 'string',
        'alias' => 'string',
        'config' => 'string',
        'is_enabled' => 'bool',
        'is_feature_toggle' => 'bool',
        'start_experiment_day' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'user_id',
        'name',
        'alias',
        'config',
        'is_enabled',
        'is_feature_toggle',
        'uid',
        'owner_id',
        'start_experiment_day',
    ];

    /**
     * @return string
     */
    public static function getType(): string
    {
        return 'experiments';
    }

    /**
     * @return HasOne
     */
    public function owner(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'owner_id');
    }

    /**
     * @return HasMany
     */
    public function branches(): HasMany
    {
        return $this->hasMany(ExperimentBranches::class, 'experiment_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function experimentUsers(): HasMany
    {
        return $this->hasMany(ExperimentBranchUser::class, 'experiment_id', 'id');
    }
}
