<?php
declare(strict_types=1);

namespace Modules\AbRouter\Models\Experiments;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Modules\Auth\Models\User\User;
use Modules\Core\EntityId\EntityIdTrait;

/**
 * Class User
 * @package Modules\Auth\Models\User
 * @property int id
 * @property int owner_id
 * @property string name
 * @property string uid
 * @property string config
 * @property boolean is_enabled
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property User owner
 * @property Collection $branches
 */
class Experiment extends Model
{
    use EntityIdTrait;

    protected $casts = [
        'id' => 'int',
        'user_id' => 'int',
        'name' => 'string',
        'uid' => 'string',
        'config' => 'string',
        'is_enabled' => 'bool',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'user_id',
        'name',
        'config',
        'is_enabled',
    ];

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

    public function branches()
    {
        return $this->hasMany(ExperimentBranches::class, 'experiment_id', 'id');
    }
}
