<?php

namespace Modules\Base\Classes\Router;

use Modules\Base\Classes\Router\Route;
use Symfony\Component\Routing\RouteCollection;

class Router
{

    public $routes;
    public $paths = [];
    public $show_logs = false;
    public $file_logging = false;

    public function __construct()
    {
        $this->routes = new RouteCollection();

        $groups = (is_file(DIR_PATH . '/readme.txt')) ? ['Modules/*', '../../*/Modules/*'] : ['Modules/*'];
        foreach ($groups as $key => $group) {
            $this->paths = array_merge($this->paths, glob(DIR_PATH . $group));
        }

    }

    public function fetchRoutes()
    {

        /*if (Cache::has("fetch_right_last_path")) {
        $last_path = Cache::get("fetch_right_last_path", '');
        }*/

        $gapi_file = DIR_PATH . 'Routes' . DIRECTORY_SEPARATOR . 'api.php';
        if (file_exists($gapi_file)) {
            include $gapi_file;
        }

        $gweb_file = DIR_PATH . DIRECTORY_SEPARATOR . 'Routes' . DIRECTORY_SEPARATOR . 'web.php';
        if (file_exists($gweb_file)) {
            include $gweb_file;
        }

        $this->paths = [];

        foreach ($this->paths as $key => $path) {

            $api_file = $path . DIRECTORY_SEPARATOR . 'Routes' . DIRECTORY_SEPARATOR . 'api.php';
            if (file_exists($api_file)) {
                include $api_file;
            }

            $web_file = $path . DIRECTORY_SEPARATOR . 'Routes' . DIRECTORY_SEPARATOR . 'web.php';
            if (file_exists($web_file)) {
                include $web_file;
            }

        }

        return $this->routes;
    }

    public function add_route($url, $controller = "", $name = "", $method = ['GET'], )
    {
        $route = new Route($url, ['_controller' => $controller]);
        $route->setMethods($method);

        $this->routes->add($name, $route);

        return $route;
    }

    public function post($url, $controller = "", $name = "", )
    {
        return $this->add_route($url, $controller, $name, ['GET']);
    }

    public function get($url, $controller, $name = "", )
    {
        return $this->add_route($url, $controller, $name, ['POST']);
    }

}
