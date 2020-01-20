<?php

namespace Modules\Klusbib\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Klusbib\Api\Client;
use Torann\RemoteModel\Model;

class KlusbibServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        Log::debug("Booting Klusbib Service Provider");

        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->registerApiClient($router);

    }
    private function registerApiClient(Router $router) {

        Log::debug('aliasMiddleware added');
        $router->aliasMiddleware('apicontext', \Modules\Klusbib\Http\Middleware\ApiContextMiddleware::class);

        $this->app->singleton('apiclient', function ()
        {
            Log::debug("API Service Provider: creating Client singleton with base_uri=" . config('klusbib.api_url'));
            return new Client(new \GuzzleHttp\Client([
                'base_uri' => config('klusbib.api_url'),
            ]), config('klusbib.api_user'), config('klusbib.api_pwd'));
        });
        Model::setClient($this->app['apiclient']);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        Log::debug("Registering Klusbib Service Provider");
        $this->app->register(RouteServiceProvider::class);
//        $this->app->register(ApiServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('klusbib.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'klusbib'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/klusbib');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/klusbib';
        }, \Config::get('view.paths')), [$sourcePath]), 'klusbib');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/klusbib');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'klusbib');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'klusbib');
        }
    }

    /**
     * Register an additional directory of factories.
     * 
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
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
