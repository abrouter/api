<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\EntityId\EntityIdTrait;
use Modules\Core\EntityId\ResourceIdInterface;

/**
 * Class SentEmailsLogs
 * @package Modules\ProxiedMail\Models
 * @property int id
 * @property int proxy_binding_id
 * @property string to
 * @property string from
 * @property string meta
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class SentEmailsLogs extends Model implements ResourceIdInterface
{
    use EntityIdTrait;

    protected $casts = [
        'id' => 'int',
        'proxy_binding_id' => 'int',
        'to' => 'string',
        'from' => 'string',
        'meta' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'proxy_binding_id',
        'to',
        'from',
        'meta',
    ];

    public static function getType(): string
    {
        return 'sent_emails_logs';
    }
}
