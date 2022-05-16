<?php
declare(strict_types=1);

namespace Modules\AbRouter\Models\CustomizationEvent;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Modules\Auth\Models\User\User;
use Modules\Core\EntityId\IdTrait;

/**
 * Class Event
 * @package Modules\AbRouter\Models\CustomizationEvent
 * @property int id
 * @property string user_id
 * @property string event_name
 * @property string type
 * @property string order
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property User user
 */
class DisplayUserEvent extends Model
{
    use IdTrait;
    
    protected $casts = [
        'id' => 'int',
        'user_id' => 'int',
        'event_name' => 'string',
        'type' => 'string',
        'order' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'user_id',
        'event_name',
        'type',
        'order',
    ];

    public static function getType(): string
    {
        return 'display_user_events';
    }

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
