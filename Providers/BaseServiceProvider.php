<?php

namespace Modules\Base\Providers;

use App\Models\User;
use Artisan;
use Config;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Modules\Base\Classes\Datasetter;
use Modules\Core\Entities\Setting;
use Modules\Partner\Entities\Partner;
use Mybizna\Automigrator\Commands\MigrateCommand;

class BaseServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Base';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'base';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {

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

        $this->commands([
            \Modules\Base\Console\Commands\DataProcessor::class,
        ]);

        if (!App::runningInConsole()) {
            // app is running in console
            $this->runMigration();
        }

        $this->setGlobalVariables();

        require_once base_path() . '/Modules/Base/Helpers/GlobalFunctions.php';

        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

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
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
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

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $paths = [];

        $groups = (is_file(base_path('../readme.txt'))) ? ['Modules/*', '../../*/Modules/*'] : ['Modules/*'];

        foreach ($groups as $key => $group) {
            $paths = array_merge($paths, glob(base_path($group)));
        }

        foreach ($paths as $key => $path) {
            $path_arr = array_reverse(explode('/', $path));
            $module_name = $path_arr[0];

            if (is_dir($path . DIRECTORY_SEPARATOR . 'Resources/lang')) {
                $this->loadTranslationsFrom(module_path($module_name, 'Resources/lang'), Str::lower($module_name));
            }

        }

    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        if (!Schema::hasTable('core_setting')) {
            return;
        }

        $paths = [];
        $merged_settings = [];

        $groups = (is_file(base_path('../readme.txt'))) ? ['Modules/*', '../../*/Modules/*'] : ['Modules/*'];
        foreach ($groups as $key => $group) {
            $paths = array_merge($paths, glob(base_path($group)));
        }

        foreach ($paths as $key => $path) {
            $path_arr = array_reverse(explode('/', $path));
            $module_name = $path_arr[0];

            $module_name_l = Str::lower($module_name);
            $config_path = $path . DIRECTORY_SEPARATOR . 'settings.php';

            if (file_exists($config_path)) {

                $settings = require $config_path;

                foreach ($settings as $key => $setting) {

                    $value = $setting['value'];
                    try {
                        $db_setting = Setting::where(['module' => $module_name_l, 'name' => $key])->first();

                        if ($db_setting) {
                            $value = $db_setting->value;
                        }
                    } catch (\Throwable $th) {
                        //throw $th;
                    }

                    $merged_settings[$key] = $value;

                }

                $config = $this->app['config']->get($module_name_l, []);
                $this->app['config']->set($module_name_l, array_merge($merged_settings, $config));

            }
        }

    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {

        $paths = [];

        $groups = (is_file(base_path('../readme.txt'))) ? ['Modules/*', '../../*/Modules/*'] : ['Modules/*'];
        foreach ($groups as $key => $group) {
            $paths = array_merge($paths, glob(base_path($group)));
        }

        foreach ($paths as $key => $path) {
            $path_arr = array_reverse(explode('/', $path));
            $module_name = $path_arr[0];

            $viewPath = resource_path('views/modules/' . Str::lower($module_name));

            $sourcePath = $path . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'views';

            if (is_dir($sourcePath)) {
                $this->publishes([
                    $sourcePath => $viewPath,
                ], 'views');

                $this->loadViewsFrom(array_merge(array_map(function ($path) use ($module_name) {
                    return $path . '/modules/' . Str::lower($module_name);
                }, \Config::get('view.paths')), [$sourcePath]), Str::lower($module_name));
            }

        }

    }

    /**
     * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
     * Run Migration
     * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
     * */
    public function runMigrationOld()
    {
        $migrate_command = new MigrateCommand();
        $datasetter = new Datasetter();

        $paths = [];

        $this->initializeConfig();
        // $this->runSessionNCacheMigration();

        $groups = (is_file(base_path('../readme.txt'))) ? ['Modules/*', '../../*/Modules/*'] : ['Modules/*'];

        foreach ($groups as $key => $group) {
            $paths = array_merge($paths, glob(base_path($group)));
        }

        $modules = [];
        $new_versions = [];
        $need_migration = false;
        $versions = $this->getVersions();

        foreach ($paths as $key => $path) {
            $path_arr = array_reverse(explode('/', $path));
            $module_name = $path_arr[0];

            $composer = $this->getComposer($path);

            if (!isset($versions[$module_name]) || $versions[$module_name] != $composer['version']) {
                $need_migration = true;
            }

            $modules[$module_name] = true;
            $new_versions[$module_name] = $composer['version'];
        }

        ksort($modules);
        ksort($new_versions);

        Cache::forget('mybizna_base_modules');
        Cache::forget('mybizna_base_versions');

        Cache::forever('mybizna_base_modules', $modules);
        Cache::forever('mybizna_base_versions', $new_versions);

        if ($need_migration) {
            Artisan::call('migrate');
            $migrate_command->migrateModels(true);
            $this->initiateUser();
            $datasetter->dataProcess();
        }
    }

    protected function runMigration()
    {

        if (!Schema::hasTable('cache') && !$this->migrationFileExists('create_cache_table')) {
            Artisan::call('cache:table');
        }

        if (!Schema::hasTable('sessions') && !$this->migrationFileExists('create_sessions_table')) {
            Artisan::call('session:table');
        }

        if (!Schema::hasTable('cache') || !Schema::hasTable('sessions')) {
            Artisan::call('migrate');
        }

        $this->initiateUser();

    }

    protected function migrationFileExists($mgr)
    {
        $path = database_path('migrations/');
        $files = scandir($path);
        $pos = false;
        foreach ($files as &$value) {
            $pos = strpos($value, $mgr);
            if ($pos !== false) {
                return true;
            }

        }
        return false;
    }

    private function initiateUser()
    {
        $partner = new Partner();

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
                    'email' => $wp_user->user_email,
                ];

                $partner->createPartner($data);
            }
        }

    }

    private function getVersions()
    {
        if (Cache::has('mybizna_base_versions')) {
            return Cache::get('mybizna_base_versions');
        }

        return [];
    }

    private function getComposer($path)
    {
        $path = $path . DIRECTORY_SEPARATOR . 'composer.json';

        $json = file_get_contents($path);

        return json_decode($json, true);
    }

    private function initializeConfig()
    {
        $logging_config = $this->app['config']->get('logging', []);
        $logging_config['channels']['datasetter'] = [
            'driver' => 'single',
            'path' => storage_path('logs/datasetter.log'),
        ];
        $this->app['config']->set('logging', $logging_config);

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
