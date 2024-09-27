<?php

namespace Modules\Base\Providers;

use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Modules\Partner\Classes\Partner;

class BaseServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Base';

    protected string $moduleNameLower = 'base';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));

        if (defined('DB_NAME')) {
            Config::set('app.url', MYBIZNA_URL);
            Config::set('app.key', MYBIZNA_APPKEY);
            Config::set('database.connections.mysql.database', DB_NAME);
            Config::set('database.connections.mysql.username', DB_USER);
            Config::set('database.connections.mysql.password', DB_PASSWORD);
            Config::set('database.connections.mysql.host', DB_HOST);

            Config::set('cache.default', "database");
            Config::set('session.driver', "database");
            //Config::set('session.domain', MYBIZNA_URL);
            //Config::set('santum.stateful', MYBIZNA_URL);
        }

        $this->initiateUser();

        $this->commands([
            \Modules\Base\Console\Commands\DataProcessor::class,
        ]);

        $this->setGlobalVariables();

        require_once base_path() . '/Modules/Base/app/Helpers/GlobalFunctions.php';

        $this->registerTranslations();
        $this->registerViews();

        if (!App::runningInConsole()) {

            $this->registerConfig();

            $config = $this->app['config']->get('mybizna', []);
            $this->app['config']->set('mybizna', array_merge(['is_local' => $this->app->isLocal()], $config));
        }

    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'lang'), $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(module_path($this->moduleName, 'lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $this->publishes([module_path($this->moduleName, 'config/config.php') => config_path($this->moduleNameLower . '.php')], 'config');
        $this->mergeConfigFrom(module_path($this->moduleName, 'config/config.php'), $this->moduleNameLower);
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);
        $sourcePath = module_path($this->moduleName, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);

        $componentNamespace = str_replace('/', '\\', config('modules.namespace') . '\\' . $this->moduleName . '\\' . ltrim(config('modules.paths.generator.component-class.path'), config('modules.paths.app_folder', '')));
        Blade::componentNamespace($componentNamespace, $this->moduleNameLower);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<string>
     */
    public function provides(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }

        return $paths;
    }

    public function setGlobalVariables()
    {

        $url = url("/");

        $composer = json_decode(file_get_contents(realpath(base_path()) . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR . 'Base' . DIRECTORY_SEPARATOR . 'composer.json'), true);
        $version = $composer['version'];

        if (request()->server->has('HTTP_X_FORWARDED_PROTO')) {
            URL::forceScheme(request()->server()['HTTP_X_FORWARDED_PROTO']);
            $url = secure_url("/");
        } else {
            $version = rand(1000, 5000);
        }

        $assets_url = $url . '/mybizna/';
        $autologin = false;
        $responsive_point = 768;
        $floating_top = true;
        $margin_top = true;
        $force_https = false;

        if (strpos($url, "https://") === 0) {
            $force_https = true;
        }

        if (defined('MYBIZNA_FLOATING_TOP')) {
            $floating_top = MYBIZNA_FLOATING_TOP;
        }

        if (defined('MYBIZNA_MARGIN_TOP')) {
            $margin_top = MYBIZNA_MARGIN_TOP;
        }

        if (defined('MYBIZNA_RESPONSIVE_POINT')) {
            $responsive_point = MYBIZNA_RESPONSIVE_POINT;
        }

        if (defined('MYBIZNA_ASSETS_URL')) {
            $autologin = true;
            $assets_url = MYBIZNA_ASSETS_URL;
        }

        if (defined('MYBIZNA_BASE_URL')) {
            $url = MYBIZNA_BASE_URL;
        }

        $assets = [
            'css' => [
                ["href" => $assets_url . '/css/app.css?' . $version, 'rel' => 'stylesheet'],
                ["href" => $assets_url . '/fontawesome/css/all.min.css?' . $version, 'rel' => 'stylesheet'],
                ["href" => $assets_url . '/common/intltelinput/intlTelInput.css?' . $version, 'rel' => 'stylesheet'],
            ],
            'js' => [
                ["src" => $assets_url . '/vue3-sfc-loader/vue3-sfc-loader.js?' . $version],
                ["src" => $assets_url . '/tinymce/tinymce.min.js?' . $version],
                ["src" => $assets_url . '/tailwind/tailwindcss.js?' . $version],
                ["src" => $assets_url . '/js/app.js?' . $version, 'defer' => 'defer'],
                ["src" => $assets_url . '/common/intltelinput/intlTelInput.min.js?' . $version],
            ],
        ];

        $context = [
            'version' => $version,
            'mybizna_base_url' => $url,
            'assets_url' => $assets_url,
            'force_https' => $force_https,
            'autologin' => $autologin,
            'is_wordpress' => (defined('WP_PLUGIN_URL')) ? true : false,
            'floating_top' => $floating_top,
            'margin_top' => $margin_top,
            'responsive_point' => $responsive_point,
            'assets' => $assets,
        ];

        session(['context' => $context]);

        view()->share($context);

    }

    private function initiateUser()
    {
        $partner = new Partner();

        // Check if user table is exist
        if (!Schema::hasTable('users')) {
            return;
        }

        $userCount = User::count();

        if (!$userCount) {

            if (defined('MYBIZNA_USER_LIST')) {
                $wp_user_list = MYBIZNA_USER_LIST;

                foreach ($wp_user_list as $key => $wp_user) {

                    $user_cls = new User();
                    $user_cls->password = Hash::make(uniqid());
                    $user_cls->email = $wp_user->user_email;
                    $user_cls->name = $wp_user->user_nicename;
                    $user_cls->save();

                    // administrator,editor,author,contributor,subscriber
                    foreach ($wp_user->roles as $key => $role) {

                        switch ($role) {
                            case 'administrator':
                                $user_cls->assignRole('administrator');
                                break;
                            case 'editor':
                                $user_cls->assignRole('manager');
                                break;
                            case 'author':
                                $user_cls->assignRole('supervisor');
                                break;
                            case 'contributor':
                                $user_cls->assignRole('staff');
                                break;
                            case 'subscriber':
                            default:
                                $user_cls->assignRole('registered');
                                break;
                        }
                    }

                    $name_arr = explode(' ', $wp_user->display_name);

                    $data = [
                        'user_id' => $user_cls->id,
                        'first_name' => $name_arr[0],
                        'last_name' => $name_arr[1] ?? '',
                        'type_str' => 'customer',
                        'email' => $wp_user->user_email,
                    ];

                    $partner->createPartner($data);
                }

            } else {
                $user_cls = new User();

                $user_cls->password = Hash::make('admin');
                $user_cls->email = 'admin@admin.com';
                $user_cls->name = 'Admin User';
                $user_cls->save();

                $data = [
                    'user_id' => $user_cls->id,
                    'first_name' => 'Admin',
                    'last_name' => 'User',
                    'type_str' => 'customer',
                    'email' => $user_cls->email,
                ];

                $partner->createPartner($data);
            }
        }

    }

}
