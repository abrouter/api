<?php
declare(strict_types=1);

namespace Modules\Auth\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * Class PasswordReset
 * @package Modules\Auth\Models
 * @property int id
 * @property string emaik
 * @property string token
 * @property Carbon created_at
 */
class PasswordReset extends Model
{
    use Notifiable;

    const UPDATED_AT = null;

    protected $casts = [
        'email' => 'string',
        'token' => 'string',
        'created_at' => 'datetime',
    ];

    protected $fillable = [
        'email',
        'token',
    ];

    public static function getType(): string
    {
        return 'pasword_reset';
    }
}