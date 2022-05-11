<?php
declare(strict_types=1);

namespace Modules\Auth\Models\User;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Modules\Core\EntityId\IdTrait;

/**
 * @property int id
 * @property integer user_id
 * @property string token
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class UserShortToken extends Model
{
    use Notifiable, HasApiTokens, IdTrait;

    protected $casts = [
        'id' => 'int',
        'user_id' => 'int',
        'token' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'token',
        'user_id',
    ];
}
