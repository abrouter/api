<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Modules\Auth\Models\User\User;
use Modules\Core\EntityId\EntityIdTrait;
use Modules\Core\EntityId\ResourceIdInterface;

/**
 * Class ProxyBinding
 * @package Modules\ProxiedMail\Models
 * @property int id
 * @property int user_id
 * @property string proxy_address
 * @property ProxyBinding|null reverse
 * @property ProxyBinding|null reverseFor
 * @property integer received_emails
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Collection realAddresses
 * @property User user
 */
class ProxyBinding extends Model implements ResourceIdInterface
{
    use EntityIdTrait;

    protected $casts = [
        'id' => 'int',
        'user_id' => 'int',
        'proxy_address' => 'string',
        'received_emails' => 'integer',
        'reverse_for' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'user_id',
        'proxy_address',
        'reverse_for',
        'received_emails',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public static function getType(): string
    {
        return 'proxy_bindings';
    }

    /**
     * @return HasOne|null
     */
    public function reverse(): HasOne
    {
        return $this->hasOne(ProxyBinding::class, 'reverse_for', 'id');
    }

    public function reverseFor(): HasOne
    {
        return $this->hasOne(ProxyBinding::class, 'id', 'reverse_for');
    }

    public function realAddresses(): HasMany
    {
        return $this->hasMany(RealAddressesGroups::class, 'proxy_binding_id', 'id');
    }
}
