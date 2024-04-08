<?php

namespace Modules\Base\Classes\Router;

use Modules\Base\Classes\Router\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Router class
 *
 * This class is used to create routes
 *
 * @package Modules\Base\Classes\Router
 */
class Router
{
    /**
     * Routes
     *
     * @var RouteCollection
     */
    public $routes;

    /**
     * Paths
     *
     * @var array
     */
    public $paths = [];

    /**
     * Show logs
     *
     * @var boolean
     */
    public $show_logs = false;

    /**
     * File logging
     *
     * @var boolean
     */
    public $file_logging = false;


    /**
     * Router constructor.
     *
     * The constructor is used to fetch the paths
     */
    public function __construct()
    {
        $this->routes = new RouteCollection();

        $groups = (is_file(DIR_PATH . '/readme.txt')) ? ['Modules/*', '../../*/Modules/*'] : ['Modules/*'];
        foreach ($groups as $key => $group) {
            $this->paths = array_merge($this->paths, glob(DIR_PATH . $group));
        }

    }

    /**
     * Fetch Routes
     *
     * The function is used to fetch the routes
     * 
     * @return RouteCollection
     */
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

    /**
     * Add Route
     *
     * The function is used to add a route
     * 
     * @param string $url
     * @param string $controller
     * @param string $name
     * @param array $method
     * 
     * @return Route
     */
    public function add_route($url, $controller = "", $name = "", $method = ['GET'], )
    {
        $route = new Route($url, ['_controller' => $controller]);
        $route->setMethods($method);

        $this->routes->add($name, $route);

        return $route;
    }

    /**
     * Post
     *
     * The function is used to add a post route
     * 
     * @param string $url
     * @param string $controller
     * @param string $name
     * 
     * @return Route
     */
    public function post($url, $controller = "", $name = "", )
    {
        return $this->add_route($url, $controller, $name, ['GET']);
    }

    /**
     * Get
     *
     * The function is used to add a get route
     * 
     * @param string $url
     * @param string $controller
     * @param string $name
     * 
     * @return Route
     */
    public function get($url, $controller, $name = "", )
    {
        return $this->add_route($url, $controller, $name, ['POST']);
    }

}
