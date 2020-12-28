<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Models;

use Carbon\Carbon;
use Modules\Core\EntityId\EntityIdTrait;
use Modules\Core\EntityId\ResourceIdInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ReceivedEmail
 * @package Modules\ProxiedMail\Models
 * @property int id
 * @property string payload
 * @property string recipient_email
 * @property string sender_email
 * @property bool is_processed
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class ReceivedEmail extends Model implements ResourceIdInterface
{
    use EntityIdTrait;

    protected $casts = [
        'id' => 'int',
        'payload' => 'string',
        'recipient_email' => 'string',
        'sender_email' => 'string',
        'is_processed' => 'bool',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'payload',
        'recipient_email',
        'sender_email',
        'is_processed',
    ];

    public static function getType(): string
    {
        return 'received-emails';
    }
}
