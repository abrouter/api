<?php
declare(strict_types=1);

namespace Modules\AbRouter\Models\Events;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Modules\AbRouter\Models\RelatedUsers\RelatedUser;
use Modules\Auth\Models\User\User;
use Modules\Core\EntityId\IdTrait;

/**
 * Class Event
 * @package Modules\AbRouter\Models\Experiments
 * @property int id
 * @property int owner_id
 * @property string temporary_user_id
 * @property string user_id
 * @property string event
 * @property string value
 * @property string tag
 * @property string ip
 * @property string referrer
 * @property string meta
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property User owner
 * @property string country_code
 * @property Collection relatedUsers
 */
class Event extends Model
{
    use IdTrait;

    protected $casts = [
        'id' => 'int',
        'owner_id' => 'int',
        'temporary_user_id' => 'string',
        'user_id' => 'string',
        'event' => 'string',
        'value' => 'string',
        'tag' => 'string',
        'referrer' => 'string',
        'ip'=> 'string',
        'meta' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'country_code' => 'string',
    ];

    protected $fillable = [
        'owner_id',
        'temporary_user_id',
        'user_id',
        'event',
        'value',
        'tag',
        'referrer',
        'meta',
        "ip",
        'created_at',
        'country_code',
    ];

    public static function getType(): string
    {
        return 'events';
    }
    
    /**
     * @return HasOne
     */
    public function owner(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'owner_id');
    }
    
    public function relatedUsers(): HasMany
    {
        if(empty($this->user_id)) {
            return $this->hasMany(RelatedUser::class, 'event_id', 'id');
        }

        return $this
            ->hasMany(RelatedUser::class, 'user_id', 'user_id')
            ->union($this->hasMany(RelatedUser::class, 'related_user_id', 'user_id')->toBase())
            ->union($this->hasMany(RelatedUser::class, 'event_id', 'id')->toBase());
    }
}
