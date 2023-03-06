<?php

namespace Modules\Base\Providers;

use Config;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Modules\Core\Entities\Setting;

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
        $this->commands([
            \Modules\Base\Console\Commands\DataProcessor::class,
        ]);

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

        $DS = DIRECTORY_SEPARATOR;
        $url = url("/");

        $composer = json_decode(file_get_contents(realpath(base_path()) . $DS . 'Modules' . $DS . 'Base' . $DS . 'composer.json'), true);
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

        view()->share([
            'version' => $version,
            'mybizna_base_url' => $url,
            'assets_url' => $assets_url,
            'autologin' => $autologin,
            'floating_top' => $floating_top,
            'margin_top' => $margin_top,
            'responsive_point' => $responsive_point,
        ]);

    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $paths = [];

        $DS = DIRECTORY_SEPARATOR;

        $groups = (is_file('../readme.txt')) ? ['Modules/*', '../../*/Modules/*'] : ['Modules/*'];

        foreach ($groups as $key => $group) {
            $paths = array_merge($paths, glob(base_path($group)));
        }

        foreach ($paths as $key => $path) {
            $path_arr = array_reverse(explode('/', $path));
            $module_name = $path_arr[0];

            if (is_dir($path . $DS . 'Resources/lang')) {
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
        // TODO: Rework using setting

        $DS = DIRECTORY_SEPARATOR;

        $paths = [];

        $groups = (is_file('../readme.txt')) ? ['Modules/*', '../../*/Modules/*'] : ['Modules/*'];
        foreach ($groups as $key => $group) {
            $paths = array_merge($paths, glob(base_path($group)));
        }

        foreach ($paths as $key => $path) {
            $path_arr = array_reverse(explode('/', $path));
            $module_name = $path_arr[0];

            $module_name_l = Str::lower($module_name);
            $config_path = $path . $DS . 'settings.php';

            if (file_exists($config_path)) {

                $settings = require $config_path;

                foreach ($settings as $key => $setting) {

                    $value = $setting['value'];
                    $db_setting = Setting::where(['module' => $module_name_l, 'name' => $key])->first();

                    if ($db_setting) {
                        $value = $db_setting->value;
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

        $DS = DIRECTORY_SEPARATOR;

        $paths = [];

        $groups = (is_file('../readme.txt')) ? ['Modules/*', '../../*/Modules/*'] : ['Modules/*'];
        foreach ($groups as $key => $group) {
            $paths = array_merge($paths, glob(base_path($group)));
        }

        foreach ($paths as $key => $path) {
            $path_arr = array_reverse(explode('/', $path));
            $module_name = $path_arr[0];

            $viewPath = resource_path('views/modules/' . Str::lower($module_name));
            
            $sourcePath = $path . $DS . 'Resources' . $DS . 'views';

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
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
