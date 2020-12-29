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
class Experiment extends Model
{
    protected $casts = [
        'id' => 'int',
        'user_id' => 'int',
        'name' => 'string',
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
}
