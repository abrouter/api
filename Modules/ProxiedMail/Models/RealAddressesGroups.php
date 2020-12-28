<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class RealAddressesGroups
 * @property int id
 * @property int proxy_binding_id
 * @property string real_address
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property ProxyBinding proxyBinding
 */
class RealAddressesGroups extends Model
{
    protected $casts = [
        'id' => 'int',
        'proxy_binding_id' => 'integer',
        'real_address' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'proxy_binding_id',
        'real_address',
    ];

    public function proxyBinding(): HasOne
    {
        return $this->hasOne(ProxyBinding::class, 'id', 'proxy_binding_id');
    }
}
