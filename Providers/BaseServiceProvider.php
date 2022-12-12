<?php

namespace Modules\Base\Providers;

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
        require_once base_path() . '/Modules/Base/Helpers/GlobalFunctions.php';

        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();

        $config = $this->app['config']->get('mybizna', []);
        $this->app['config']->set('mybizna', array_merge(['is_local' => $this->app->isLocal()], $config));

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
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $DS = DIRECTORY_SEPARATOR;
        $modules_path = realpath(base_path()) . $DS . 'Modules';

        if (is_dir($modules_path)) {
            $dir = new \DirectoryIterator($modules_path);

            foreach ($dir as $fileinfo) {
                if (!$fileinfo->isDot() && $fileinfo->isDir()) {
                    $module_name = $fileinfo->getFilename();

                    if (is_dir($modules_path . $DS . $module_name . $DS . 'Resources/lang')) {
                        $this->loadTranslationsFrom(module_path($module_name, 'Resources/lang'), Str::lower($module_name));
                    }
                }
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
        $modules_path = realpath(base_path()) . $DS . 'Modules';

        if (is_dir($modules_path)) {
            $dir = new \DirectoryIterator($modules_path);

            foreach ($dir as $fileinfo) {
                if (!$fileinfo->isDot() && $fileinfo->isDir()) {
                    $merged_settings = [];
                    $module_name = $fileinfo->getFilename();
                    $module_name_l = Str::lower($module_name);
                    $config_path = $modules_path . $DS . $module_name . $DS . 'settings.php';

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
        $modules_path = realpath(base_path()) . $DS . 'Modules';

        if (is_dir($modules_path)) {
            $dir = new \DirectoryIterator($modules_path);

            foreach ($dir as $fileinfo) {
                if (!$fileinfo->isDot() && $fileinfo->isDir()) {
                    $module_name = $fileinfo->getFilename();

                    $viewPath = resource_path('views/modules/' . Str::lower($module_name));

                    $sourcePath = $modules_path . $DS . $module_name . $DS . 'Resources' . $DS . 'views';

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
