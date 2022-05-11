<?php
declare(strict_types=1);

namespace Modules\Auth\Providers;

use Illuminate\Foundation\Application;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Modules\Auth\Integration\AuthGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Modules\Auth\Integration\AuthClient;
use Modules\Auth\Integration\UserProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
    ];

    public function boot()
    {
        $this->registerPolicies();

        $this->registerPassportExpirePolicies();
        $this->registerGuardDriver();
        $this->registerUserProvider();
    }

    private function registerGuardDriver() : void
    {
        Auth::extend('oauth', function ($app, $name, array $config) {
            /**
             * @var Application $app
             */
            return new AuthGuard(
                Auth::createUserProvider($config['provider']),
                $app->make(Request::class),
                $app->make(AuthClient::class)
            );
        });
    }

    private function registerUserProvider(): void
    {
        Auth::provider('oauth_eloquent', function ($app, array $config) {
            return new UserProvider($app['hash'], $config['model']);
        });
    }

    private function registerPassportExpirePolicies():void
    {
        Passport::tokensExpireIn(now()->addDays(360));
        Passport::refreshTokensExpireIn(now()->addDays(900));
        Passport::personalAccessTokensExpireIn(now()->addYears(10));
    }
}
