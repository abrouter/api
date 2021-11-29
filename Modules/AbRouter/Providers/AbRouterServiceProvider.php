<?php

namespace Modules\AbRouter\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class AbRouterServiceProvider extends ServiceProvider
{

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->commands([\Modules\AbRouter\Console\CreateDatabase::class]);
        $this->commands([\Modules\AbRouter\Console\DropDatabase::class]);
        $this->commands([\Modules\AbRouter\Console\MigrateTemporaryUser::class]);
        $this->loadMigrationsFrom(module_path('AbRouter', 'Database/Migrations'));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path('AbRouter', 'Config/config.php') => config_path('abrouter.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('Abrouter', 'Config/config.php'),
            'abrouter'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $sourcePath = module_path('AbRouter', 'Resources/views');

        View::addNamespace('abrouter', $sourcePath);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/abrouter');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'abrouter');
        } else {
            $this->loadTranslationsFrom(module_path('AbRouter', 'Resources/lang'), 'abrouter');
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
            app(Factory::class)->load(module_path('AbRouter', 'Database/factories'));
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
