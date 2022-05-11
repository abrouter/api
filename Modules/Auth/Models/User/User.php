<?php
declare(strict_types=1);

namespace Modules\Auth\Models\User;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Modules\Core\EntityId\IdTrait;

/**
 * Class User
 * @package Modules\Auth\Models\User
 * @property int id
 * @property string username
 * @property string password
 * @property string google_id
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class User extends Authenticatable
{
    use Notifiable, HasApiTokens, IdTrait;

    protected $casts = [
        'id' => 'int',
        'username' => 'string',
        'password' => 'string',
        'google_id' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'username',
        'password',
        'google_id'
    ];

    public static function getType(): string
    {
        return 'users';
    }
}
