<?php

namespace Modules\ProxiedMail\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\ProxiedMail\Commands\ForwardEmailsCommand;

class ModuleServiceProvider extends ServiceProvider
{
    private const COMMANDS = [
        ForwardEmailsCommand::class,
    ];

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands(self::COMMANDS);

        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(module_path('ProxiedMail', 'Database/Migrations'));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path('ProxiedMail', 'Config/config.php') => config_path('proxiedmail.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('ProxiedMail', 'Config/config.php'),
            'proxiedmail'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $sourcePath = module_path('ProxiedMail', 'Resources/views');

        View::addNamespace('proxiedmail', $sourcePath);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/proxiedmail');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'proxiedmail');
        } else {
            $this->loadTranslationsFrom(module_path('ProxiedMail', 'Resources/lang'), 'proxiedmail');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(module_path('ProxiedMail', 'Database/factories'));
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
