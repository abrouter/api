<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\ProxiedMail\Events\ReceivedEmail\ReceivedEmailCreated;
use Modules\ProxiedMail\Listeners\ReceivedEmail\ReceivedEmailListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ReceivedEmailCreated::class => [
            ReceivedEmailListener::class,
        ],
    ];
}
