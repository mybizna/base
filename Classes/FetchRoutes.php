<?php

namespace Modules\Base\Classes;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class FetchRoutes
{

    public $routes = [];
    public $layouts = [];

    public function fetchRoutes()
    {
        $DS = DIRECTORY_SEPARATOR;

        $modules_path = realpath(base_path()) . $DS . 'Modules';

        if (Cache::has("fetch_routes") && Cache::has("fetch_layouts")) {
            $this->layouts = Cache::get("fetch_layouts");
            $this->routes = Cache::get("fetch_routes");
        } else {

            if (is_dir($modules_path)) {

                $routes = [];

                $dir = new \DirectoryIterator($modules_path);

                foreach ($dir as $fileinfo) {
                    if (!$fileinfo->isDot() && $fileinfo->isDir()) {
                        $module_name = $fileinfo->getFilename();

                        if ($module_name != '') {
                            $routes = $this->getModuleRoute($module_name, $routes);
                        }

                        $routes_file = $modules_path . DIRECTORY_SEPARATOR . $module_name . DIRECTORY_SEPARATOR . 'routes.json';

                        if (file_exists($routes_file)) {
                            $routes_arr = json_decode(file_get_contents($routes_file), true);
                            if (!empty($routes_arr)) {
                                $routes = array_merge($routes, $routes_arr);
                            }
                        }
                    }
                }

                $this->routes = array_merge($this->routes, $routes);

                Cache::put("fetch_layouts", $this->routes, 3600);
                Cache::put("fetch_routes", $this->layouts, 3600);
            }
        }

        return ['routes' => $this->routes, 'layouts' => $this->layouts];
    }

    public function getModuleRoute($module_name, $routes)
    {
        $DS = DIRECTORY_SEPARATOR;

        $module_path = realpath(base_path()) . $DS . 'Modules' . $DS . $module_name;

        $m_folder_path = $module_name;
        $module_route = $this->addRouteToList('/' . $module_name, $m_folder_path, 'router_view');

        foreach (['admin', 'web'] as $folder) {
            $vue_folders = $module_path . $DS . 'views' . $DS . $folder;

            $f_folder_path = $m_folder_path . '/' . $folder;
            $folder_route = $this->addRouteToList($folder, $f_folder_path, 'router_view');

            if (is_dir($vue_folders)) {
                $dir = new \DirectoryIterator($vue_folders);

                foreach ($dir as $fileinfo) {
                    if (!$fileinfo->isDot() && $fileinfo->isDir()) {
                        $vs_foldername = $fileinfo->getFilename();
                        $vs_folders = $vue_folders . $DS . $vs_foldername;
                        $v_folder_path = $f_folder_path . '/' . $vs_foldername;

                        $vs_route = $this->addRouteToList($vs_foldername, $v_folder_path, 'router_view');

                        if (is_dir($vs_folders)) {
                            $vs_dir = new \DirectoryIterator($vs_folders);

                            foreach ($vs_dir as $vs_fileinfo) {
                                if (!$vs_fileinfo->isDot() && !$vs_fileinfo->isDir()) {
                                    $vs_filename = $vs_fileinfo->getFilename();
                                    $vs_sx_filename = str_replace('.vue', '', $vs_filename);
                                    $vs_path = $module_name . '/' . $folder . '/' . $vs_foldername . '/' . $vs_sx_filename;

                                    $t_folder_path = $v_folder_path . '/' . $vs_sx_filename;

                                    if ($vs_sx_filename == 'list') {
                                        $vs_route['children'][] = $this->addRouteToList($vs_sx_filename, $t_folder_path, $vs_path . '.vue', true);
                                        $vs_route['children'][] = $this->addRouteToList($vs_sx_filename, $t_folder_path, $vs_path . '.vue');
                                    } else {
                                        if (in_array($vs_filename, ['edit.vue', 'modify.vue', 'detail.vue', 'update.vue'])) {
                                            $vs_sx_filename = $vs_sx_filename . '/:id';
                                        }
                                        $vs_route['children'][] = $this->addRouteToList($vs_sx_filename, $t_folder_path, $vs_path . '.vue');
                                    }

                                    if (!in_array($vs_filename, ['create.vue', 'edit.vue', 'modify.vue', 'new.vue', 'detail.vue', 'update.vue', 'form.vue'])) {
                                        $this->layouts[$module_name][$folder][$vs_foldername][] = $vs_filename;
                                    }
                                }
                            }
                        }

                        if (!empty($vs_route['children'])) {
                            $folder_route['children'][] = $vs_route;
                        }
                    }
                }
            }

            if (!empty($folder_route['children'])) {
                $module_route['children'][] = $folder_route;
            }
        }

        if (!empty($module_route['children'])) {
            $routes[] = $module_route;
        }

        return $routes;
    }

    public function addRouteToList($path, $name, $component, $no_path = false)
    {
        return [
            'path' => $no_path ? '' : Str::lower($path),
            'meta' => ['breadcrumb' => true, 'middlewareAuth' => true],
            'name' => $no_path ? Str::lower(str_replace('/', '.', $name)) . '.default' : Str::lower(str_replace('/', '.', $name)),
            'component' => Str::lower($component),
        ];
    }
}
