<?php

namespace Modules\Klusbib\Providers;

use App\Models\Accessory;
use App\Models\Asset;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Klusbib\Api\Client;
use Modules\Klusbib\Models\Api\Delivery;
use Modules\Klusbib\Models\Api\Lending;
use Modules\Klusbib\Models\Api\Membership;
use Modules\Klusbib\Models\Api\Payment;
use Modules\Klusbib\Models\Api\Reservation;
use Modules\Klusbib\Models\Api\User;
use Modules\Klusbib\Models\AssetTagPattern;
use Modules\Klusbib\Observers\AssetObserver;
use Modules\Klusbib\Notifications\NotifyAccessoryCheckin;
use Modules\Klusbib\Notifications\NotifyAccessoryCheckout;
use Modules\Klusbib\Notifications\NotifyAssetCheckin;
use Modules\Klusbib\Notifications\NotifyAssetCheckout;
use Modules\Klusbib\Policies\DeliveryPolicy;
use Modules\Klusbib\Policies\LendingPolicy;
use Modules\Klusbib\Policies\MembershipPolicy;
use Modules\Klusbib\Policies\PaymentPolicy;
use Modules\Klusbib\Policies\ReservationPolicy;
use Modules\Klusbib\Policies\UserPolicy;
use Torann\RemoteModel\Model;
use Gate;

class KlusbibServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Reservation::class => ReservationPolicy::class,
        Delivery::class => DeliveryPolicy::class,
        Payment::class => PaymentPolicy::class,
        Membership::class => MembershipPolicy::class,
        User::class => UserPolicy::class,
        Lending::class => LendingPolicy::class
    ];

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
        $this->updatePermissions();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->registerApiClient($router);

        $this->registerNotifications();
//        $this->registerCommands();
        $this->registerObservers();
        $this->registerPolicies();

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
     * Update permissions array to allow config of reservations and deliveries permissions
     * @return void
     */
    protected function updatePermissions() {
        // update permissions
        $permissions = config('permissions');

        // add reservations permissions
        $reservationPermissions =     array(
            array(
                'permission' => 'klusbib.reservations.view',
                'label'      => 'View ',
                'note'       => '',
                'display'    => true,
            ),
            array(
                'permission' => 'klusbib.reservations.create',
                'label'      => 'Create ',
                'note'       => '',
                'display'    => true,
            ),
            array(
                'permission' => 'klusbib.reservations.edit',
                'label'      => 'Edit ',
                'note'       => '',
                'display'    => true,
            ),
            array(
                'permission' => 'klusbib.reservations.delete',
                'label'      => 'Delete ',
                'note'       => '',
                'display'    => true,
            ),
        );
        $permissions['Klusbib.Reservations'] = $reservationPermissions;

        // Add deliveries permissions
        $deliveryPermissions =     array(
            array(
                'permission' => 'klusbib.deliveries.view',
                'label'      => 'View ',
                'note'       => '',
                'display'    => true,
            ),
            array(
                'permission' => 'klusbib.deliveries.create',
                'label'      => 'Create ',
                'note'       => '',
                'display'    => true,
            ),
            array(
                'permission' => 'klusbib.deliveries.edit',
                'label'      => 'Edit ',
                'note'       => '',
                'display'    => true,
            ),
            array(
                'permission' => 'klusbib.deliveries.delete',
                'label'      => 'Delete ',
                'note'       => '',
                'display'    => true,
            ),
        );
        $permissions['Klusbib.Deliveries'] = $deliveryPermissions;

        // add enrolment permissions
        $enrolmentPermissions =     array(
            array(
                'permission' => 'klusbib.enrolment.view',
                'label'      => 'View ',
                'note'       => '',
                'display'    => true,
            ),
            array(
                'permission' => 'klusbib.enrolment.create',
                'label'      => 'Create ',
                'note'       => '',
                'display'    => true,
            ),
            array(
                'permission' => 'klusbib.enrolment.edit',
                'label'      => 'Edit ',
                'note'       => '',
                'display'    => true,
            ),
            array(
                'permission' => 'klusbib.enrolment.delete',
                'label'      => 'Delete ',
                'note'       => '',
                'display'    => true,
            ),
        );
        $permissions['Klusbib.Enrolments'] = $enrolmentPermissions;

        // add user permissions
        $userPermissions =     array(
            array(
                'permission' => 'klusbib.user.view',
                'label'      => 'View ',
                'note'       => '',
                'display'    => true,
            ),
            array(
                'permission' => 'klusbib.user.create',
                'label'      => 'Create ',
                'note'       => '',
                'display'    => true,
            ),
            array(
                'permission' => 'klusbib.user.edit',
                'label'      => 'Edit ',
                'note'       => '',
                'display'    => true,
            ),
            array(
                'permission' => 'klusbib.user.delete',
                'label'      => 'Delete ',
                'note'       => '',
                'display'    => true,
            ),
        );
        $permissions['Klusbib.Users'] = $userPermissions;


        // add payment permissions
        $paymentPermissions =     array(
            array(
                'permission' => 'klusbib.payment.view',
                'label'      => 'View ',
                'note'       => '',
                'display'    => true,
            ),
//            array(
//                'permission' => 'klusbib.payment.create',
//                'label'      => 'Create ',
//                'note'       => '',
//                'display'    => true,
//            ),
//            array(
//                'permission' => 'klusbib.payment.edit',
//                'label'      => 'Edit ',
//                'note'       => '',
//                'display'    => true,
//            ),
//            array(
//                'permission' => 'klusbib.payment.delete',
//                'label'      => 'Delete ',
//                'note'       => '',
//                'display'    => true,
//            ),
        );
        $permissions['Klusbib.Payments'] = $paymentPermissions;

        // add user permissions
        $lendingPermissions =     array(
            array(
                'permission' => 'klusbib.lending.view',
                'label'      => 'View ',
                'note'       => '',
                'display'    => true,
            ),
//            array(
//                'permission' => 'klusbib.lending.create',
//                'label'      => 'Create ',
//                'note'       => '',
//                'display'    => true,
//            ),
//            array(
//                'permission' => 'klusbib.lending.edit',
//                'label'      => 'Edit ',
//                'note'       => '',
//                'display'    => true,
//            ),
//            array(
//                'permission' => 'klusbib.lending.delete',
//                'label'      => 'Delete ',
//                'note'       => '',
//                'display'    => true,
//            ),
        );
        $permissions['Klusbib.Lendings'] = $lendingPermissions;
        // update permissions in config
        config(['permissions' => $permissions]);
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

    private function registerNotifications() {
        Asset::$checkoutClass = NotifyAssetCheckout::class;
        Asset::$checkinClass = NotifyAssetCheckin::class;
        Accessory::$checkoutClass = NotifyAccessoryCheckout::class;
        Accessory::$checkinClass = NotifyAccessoryCheckin::class;
    }

//    private function registerCommands() {
//        Log::debug('Regsitering Klusbib commands');
//        $kernel = $this->app->make(\Illuminate\Contracts\Console\Kernel::class);
//        $kernel->registerCommand(\Modules\Klusbib\Console\SyncLendings::class);
//    }
    private function registerObservers() {
        Log::debug('Registering Klusbib observers');
        Asset::observe(AssetObserver::class);
    }

    private function registerPolicies() {
        Log::debug('Registering Klusbib policies');
        foreach ($this->policies as $key => $value) {
            Log::debug('Registering policy ' . $key);
            Gate::policy($key, $value);
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
