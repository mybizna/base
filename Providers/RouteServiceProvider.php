<?php

namespace Modules\Base\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The module namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $moduleNamespace = 'Modules\Base\Http\Controllers';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        $DS = DIRECTORY_SEPARATOR;
        $paths = [];

        $groups = (is_file(base_path('../readme.txt'))) ? ['Modules/*', '../../*/Modules/*'] : ['Modules/*'];
        foreach ($groups as $key => $group) {
            $paths = array_merge($paths, glob(base_path($group)));
        }

        foreach ($paths as $key => $path) {
            $path_arr = array_reverse(explode('/', $path));
            $module_name = $path_arr[0];

            if (file_exists($path . $DS . 'Routes/web.php')) {
                Route::middleware('web')
                    ->namespace('Modules\\' . $module_name . '\Http\Controllers')
                    ->group(module_path($module_name, '/Routes/web.php'));
            }
        }

    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        $DS = DIRECTORY_SEPARATOR;
        $paths = [];

        $groups = (is_file(base_path('../readme.txt'))) ? ['Modules/*', '../../*/Modules/*'] : ['Modules/*'];
        foreach ($groups as $key => $group) {
            $paths = array_merge($paths, glob(base_path($group)));
        }

        foreach ($paths as $key => $path) {
            $path_arr = array_reverse(explode('/', $path));
            $module_name = $path_arr[0];

            if ($module_name != 'Base' && file_exists($path . $DS . 'Routes/api.php')) {
                Route::prefix('api')
                    ->middleware('api')
                    ->namespace('Modules\\' . $module_name . '\Http\Controllers')
                    ->group(module_path($module_name, '/Routes/api.php'));
            }

        }

        Route::prefix('api')
            ->middleware('api')
            ->group(module_path('Base', '/Routes/api.php'));

    }
}
