<?php
declare(strict_types=1);

namespace Modules\AbRouter\Models\Experiments;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\EntityId\IdTrait;

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
class ExperimentUsers extends Model
{
    use IdTrait;

    protected $casts = [
        'id' => 'int',
        'owner_id' => 'int',
        'user_signature' => 'string',
        'config' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'owner_id',
        'config',
        'user_signature',
    ];

    public static function getType(): string
    {
        return 'experiment_users';
    }
}
