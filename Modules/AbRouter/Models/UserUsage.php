<?php
declare(strict_types=1);

namespace Modules\AbRouter\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int user_id
 * @property int unique_users_count
 */
class UserUsage extends Model
{
    protected $table = 'user_usage';

    protected $casts = [
        'id' => 'int',
        'user_id' => 'int',
        'unique_users_count' => 'int',
    ];

    protected $fillable = [
        'user_id',
        'unique_users_count',
    ];
}
