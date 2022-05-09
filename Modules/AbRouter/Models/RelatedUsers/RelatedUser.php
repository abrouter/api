<?php
declare(strict_types=1);

namespace Modules\AbRouter\Models\RelatedUsers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\AbRouter\Models\Events\Event;
use Modules\Auth\Models\User\User;
use Modules\Core\EntityId\IdTrait;

/**
 * Class RelatedUser
 * @property int id
 * @property int owner_id
 * @property int event_id
 * @property string related_user_id
 * @property string user_id
 * @property Carbon created_at
 * @property User owner
 * @property Event event
 */
class RelatedUser extends Model
{
    use IdTrait;

    const UPDATED_AT = null;

    protected $casts = [
        'id' => 'int',
        'owner_id' => 'int',
        'event_id' => 'int',
        'related_user_id' => 'string',
        'user_id' => 'string',
        'created_at' => 'datetime',
    ];

    protected $fillable = [
        'owner_id',
        'related_user_id',
        'user_id',
        'event_id'
    ];

    public static function getType(): string
    {
        return 'related_users';
    }
    
    /**
     * @return HasOne
     */
    public function owner(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'owner_id');
    }
    
    public function event(): HasOne
    {
        return $this->hasOne(Event::class, 'id', 'event_id');
    }
}
