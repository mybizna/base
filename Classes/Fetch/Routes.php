<?php

namespace Modules\Base\Classes\Fetch;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Routes
{

    public $routes = [];
    public $layouts = [];
    public $paths = [];

    public function __construct()
    {
        $groups = (is_file(base_path('../readme.txt'))) ? ['Modules/*', '../../*/Modules/*'] : ['Modules/*'];
        foreach ($groups as $key => $group) {
            $this->paths = array_merge($this->paths, glob(base_path($group)));
        }

    }

    public function fetchRoutes()
    {
        Cache::forget('fetch_routes');

        if (Cache::has("fetch_routes") && Cache::has("fetch_layouts")) {
            $this->layouts = Cache::get("fetch_layouts");
            $this->routes = Cache::get("fetch_routes");
        } else {
            $routes = [];

            foreach ($this->paths as $key => $path) {

                $this->getModuleRoute($path, $routes);

            }

            //$this->routes = array_merge($this->routes, $routes);

            Cache::put("fetch_layouts", $this->layouts, 3600);
            Cache::put("fetch_routes", $this->routes, 3600);
        }

        $this->routes[] = [
            "path" => "/core/setting-manage",
            "meta" => [
                "breadcrumb" => true,
                "middlewareAuth" => true,
            ],
            "name" => "core.setting-manage",
            "component" => "core/admin/setting/manage.vue",
        ];

        return ['routes' => $this->routes, 'layouts' => $this->layouts];
    }

    public function getModuleRoute($path, $routes)
    {

        $path_arr = array_reverse(explode('/', $path));

        $m_folder_path = $module_name = $path_arr[0];

        $tmproutes = [];

        if (is_dir($path . '/Entities')) {

            $model_dir = new \DirectoryIterator($path . '/Entities');
            foreach ($model_dir as $fileinfo) {

                if (!$fileinfo->isDot() && $fileinfo->isFile() && $fileinfo->getExtension() == 'php') {

                    $model_name = $fileinfo->getFilename();
                    $model_name = Str::snake(str_replace('.php', '', $model_name));

                    $vue_name = strtolower($m_folder_path . '.admin.' . $model_name);
                    $vue_name_path = '/' . str_replace('.', '/', $vue_name);

                    $vs_route = $this->addRouteToList($model_name, $vue_name, 'router_view');

                    $meta_path = [strtolower($module_name), strtolower($model_name)];

                    $this->routes[$vue_name . '.list.default'] = $this->addRouteToList($vue_name_path . '', $vue_name . '.list.default', 'router_list', meta_path: $meta_path);
                    $this->routes[$vue_name . '.list'] = $this->addRouteToList($vue_name_path . '/list', $vue_name . '.list', 'router_list', meta_path: $meta_path);
                    $this->routes[$vue_name . '.create'] = $this->addRouteToList($vue_name_path . '/create', $vue_name . '.create', 'router_create', meta_path: $meta_path);
                    $this->routes[$vue_name . '.edit'] = $this->addRouteToList($vue_name_path . '/edit/:id', $vue_name . '.edit', 'router_edit', meta_path: $meta_path);

                    $class_name = basename(dirname($path)) . '/' . basename($path) . '/Entities/' . Str::ucfirst(Str::camel($model_name));
                    $class_name = str_replace('/', '\\', $class_name);

                    
                    
                    if (is_subclass_of($class_name, Model::class)) {
                        $object = app($class_name);
                        
                        if ($object->show_frontend) {
                            $vue_name = strtolower($m_folder_path . '.front.' . $model_name);
                            $vue_name_path = '/' . str_replace('.', '/', $vue_name);
        
                            $vue_name = strtolower($m_folder_path . '.front.' . $model_name);

                            if ($object->show_views['list']) {
                                $this->routes[$vue_name . '.list.default'] = $this->addRouteToList($vue_name_path . '', $vue_name . '.list.default', 'router_list', meta_path: $meta_path);
                                $this->routes[$vue_name . '.list'] = $this->addRouteToList($vue_name_path . '/list', $vue_name . '.list', 'router_list', meta_path: $meta_path);
                            }
                            if ($object->show_views['create']){
                                $this->routes[$vue_name . '.create'] = $this->addRouteToList($vue_name_path . '/create', $vue_name . '.create', 'router_create', meta_path: $meta_path);
                            }
                            if ($object->show_views['create']) {
                                $this->routes[$vue_name . '.edit'] = $this->addRouteToList($vue_name_path . '/edit/:id', $vue_name . '.edit', 'router_edit', meta_path: $meta_path);

                            }
                        }
                    }

                }
            }
        }

        foreach (['admin', 'web', 'front'] as $folder) {
            $vue_folders = $path . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'vue' . DIRECTORY_SEPARATOR . $folder;

            $f_folder_path = $m_folder_path . '/' . $folder;

            if (is_dir($vue_folders)) {
                $dir = new \DirectoryIterator($vue_folders);

                foreach ($dir as $fileinfo) {
                    if (!$fileinfo->isDot() && $fileinfo->isDir()) {
                        $vs_foldername = $fileinfo->getFilename();
                        $vs_folders = $vue_folders . DIRECTORY_SEPARATOR . $vs_foldername;
                        $v_folder_path = $f_folder_path . '/' . $vs_foldername;

                        if (is_dir($vs_folders)) {
                            $vs_dir = new \DirectoryIterator($vs_folders);

                            foreach ($vs_dir as $vs_fileinfo) {
                                if (!$vs_fileinfo->isDot() && !$vs_fileinfo->isDir()) {
                                    $search_path = '';
                                    $vs_filename = $vs_fileinfo->getFilename();
                                    $vs_sx_filename = str_replace('.vue', '', $vs_filename);
                                    $vs_path = $module_name . '/' . $folder . '/' . $vs_foldername . '/' . $vs_sx_filename;

                                    $vs_path_low = strtolower($vs_path);
                                    $vs_path_name = strtolower(str_replace('/', '.', $vs_path_low));

                                    $t_folder_path = $v_folder_path . '/' . $vs_sx_filename;

                                    if (File::isFile($vs_folders . '/search.vue')) {
                                        $search_path = $module_name . '/' . $folder . '/' . $vs_foldername . '/search.vue';
                                    }

                                    if ($vs_sx_filename == 'list') {
                                        $vs_path_low_default = str_replace('/list', '', $vs_path_low);
                                        $this->routes[$vs_path_name] = $this->addRouteToList('/' . $vs_path_low, $t_folder_path, $vs_path . '.vue', search_path: $search_path);
                                        $this->routes[$vs_path_name . '.default'] = $this->addRouteToList('/' . $vs_path_low_default, $t_folder_path, $vs_path . '.vue', search_path: $search_path);
                                    } else if ($vs_sx_filename == 'search') {
                                        continue;
                                    } else if ($vs_sx_filename == 'form') {
                                        if (!File::isFile($vs_folders . '/create.vue')) {
                                            $this->routes[$vs_path_name] = $this->addRouteToList('/' . $vs_path_low, $v_folder_path . '/create', $vs_path . '.vue');
                                        }

                                        if (!File::isFile($vs_folders . '/edit.vue')) {
                                            $this->routes[$vs_path_name] = $this->addRouteToList('/' . $vs_path_low . '/:id', $v_folder_path . '/edit', $vs_path . '.vue');
                                        }

                                    } else {
                                        if (in_array($vs_filename, ['edit.vue', 'modify.vue', 'detail.vue', 'update.vue'])) {
                                            $vs_path_low = $vs_path_low . '/:id';
                                        }

                                        if (in_array($vs_filename, ['create.vue', 'edit.vue', 'modify.vue', 'new.vue', 'detail.vue', 'update.vue', 'form.vue'])) {
                                            $search_path = '';
                                        }

                                        $tmp_vs_filename = str_replace('.vue', '', $vs_filename);

                                        $this->routes[$vs_path_name] = $this->addRouteToList('/' . $vs_path_low, $t_folder_path, $vs_path . '.vue', search_path: $search_path);
                                    }

                                    if (!in_array($vs_filename, ['create.vue', 'edit.vue', 'modify.vue', 'new.vue', 'detail.vue', 'update.vue', 'form.vue'])) {
                                        $this->layouts[$module_name][$folder][$vs_foldername][] = $vs_filename;
                                    }

                                }
                            }
                        }

                    }
                }

            }

        }

        return $this->routes;
    }

    public function getModuleRouteOld($path, $routes)
    {

        $path_arr = array_reverse(explode('/', $path));

        $m_folder_path = $module_name = $path_arr[0];

        $tmproutes = [];
        $tmproutes['main'] = [];
        $tmproutes['main']['admin'] = [];

        $module_route = $this->addRouteToList('/' . $module_name, $module_name, 'router_view');

        if (is_dir($path . '/Entities')) {

            $model_dir = new \DirectoryIterator($path . '/Entities');
            foreach ($model_dir as $fileinfo) {

                if (!$fileinfo->isDot() && $fileinfo->isFile() && $fileinfo->getExtension() == 'php') {

                    $model_name = $fileinfo->getFilename();
                    $model_name = Str::snake(str_replace('.php', '', $model_name));

                    $vue_name = $m_folder_path . '.admin.' . $model_name;

                    $vs_route = $this->addRouteToList($model_name, $vue_name, 'router_view');

                    $meta_path = [strtolower($module_name), strtolower($model_name)];

                    $vs_route['children']['default'] = $this->addRouteToList('', $vue_name . '.list.default', 'router_list', meta_path: $meta_path);
                    $vs_route['children']['list'] = $this->addRouteToList('list', $vue_name . '.list', 'router_list', meta_path: $meta_path);
                    $vs_route['children']['create'] = $this->addRouteToList('create', $vue_name . '.create', 'router_create', meta_path: $meta_path);
                    $vs_route['children']['edit'] = $this->addRouteToList('edit/:id', $vue_name . '.edit', 'router_edit', meta_path: $meta_path);

                    $tmproutes['main']['admin'][$model_name] = $vs_route;

                }
            }
        }

        foreach (['admin', 'web', 'front'] as $folder) {
            $vue_folders = $path . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'vue' . DIRECTORY_SEPARATOR . $folder;

            $f_folder_path = $m_folder_path . '/' . $folder;
            $folder_route = $this->addRouteToList($folder, $f_folder_path, 'router_view');

            if (is_dir($vue_folders)) {
                $dir = new \DirectoryIterator($vue_folders);

                foreach ($dir as $fileinfo) {
                    if (!$fileinfo->isDot() && $fileinfo->isDir()) {
                        $vs_foldername = $fileinfo->getFilename();
                        $vs_folders = $vue_folders . DIRECTORY_SEPARATOR . $vs_foldername;
                        $v_folder_path = $f_folder_path . '/' . $vs_foldername;

                        $tmp_route = [];
                        if (isset($tmproutes['main'][$folder][$vs_foldername])) {
                            $tmp_route = $tmproutes['main'][$folder][$vs_foldername]['children'];
                        }

                        $vs_route = $this->addRouteToList($vs_foldername, $v_folder_path, 'router_view');

                        if (is_dir($vs_folders)) {
                            $vs_dir = new \DirectoryIterator($vs_folders);

                            foreach ($vs_dir as $vs_fileinfo) {
                                if (!$vs_fileinfo->isDot() && !$vs_fileinfo->isDir()) {
                                    $search_path = '';
                                    $vs_filename = $vs_fileinfo->getFilename();
                                    $vs_sx_filename = str_replace('.vue', '', $vs_filename);
                                    $vs_path = $module_name . '/' . $folder . '/' . $vs_foldername . '/' . $vs_sx_filename;

                                    $t_folder_path = $v_folder_path . '/' . $vs_sx_filename;

                                    if (File::isFile($vs_folders . '/search.vue')) {
                                        $search_path = $module_name . '/' . $folder . '/' . $vs_foldername . '/search.vue';
                                    }

                                    if ($vs_sx_filename == 'list') {
                                        $vs_route['children'][] = $this->addRouteToList($vs_sx_filename, $t_folder_path, $vs_path . '.vue', true, search_path: $search_path);
                                        $vs_route['children'][] = $this->addRouteToList($vs_sx_filename, $t_folder_path, $vs_path . '.vue', search_path: $search_path);

                                        unset($tmproutes['main'][$folder][$vs_foldername]['children']['list']);
                                        unset($tmproutes['main'][$folder][$vs_foldername]['children']['default']);
                                    } else if ($vs_sx_filename == 'search') {
                                        continue;
                                    } else if ($vs_sx_filename == 'form') {
                                        if (!File::isFile($vs_folders . '/create.vue')) {
                                            $vs_route['children'][] = $this->addRouteToList('create', $v_folder_path . '/create', $vs_path . '.vue');
                                            unset($tmproutes['main'][$folder][$vs_foldername]['children']['create']);
                                        }

                                        if (!File::isFile($vs_folders . '/edit.vue')) {
                                            $vs_route['children'][] = $this->addRouteToList('edit/:id', $v_folder_path . '/edit', $vs_path . '.vue');
                                            unset($tmproutes['main'][$folder][$vs_foldername]['children']['edit']);
                                        }

                                    } else {
                                        if (in_array($vs_filename, ['edit.vue', 'modify.vue', 'detail.vue', 'update.vue'])) {
                                            $vs_sx_filename = $vs_sx_filename . '/:id';
                                        }

                                        if (in_array($vs_filename, ['create.vue', 'edit.vue', 'modify.vue', 'new.vue', 'detail.vue', 'update.vue', 'form.vue'])) {
                                            $search_path = '';
                                        }

                                        $tmp_vs_filename = str_replace('.vue', '', $vs_filename);

                                        unset($tmproutes['main'][$folder][$vs_foldername]['children'][$tmp_vs_filename]);

                                        $vs_route['children'][] = $this->addRouteToList($vs_sx_filename, $t_folder_path, $vs_path . '.vue', search_path: $search_path);
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

                if (isset($tmproutes['main'][$folder])) {

                    foreach ($tmproutes['main'][$folder] as $key => $model) {

                        if (empty($model['children'])) {
                            continue;
                        }

                        $vs_route = $this->addRouteToList($key, $f_folder_path . '/' . $key, 'router_view');
                        $vs_route['children'] = array_values($model['children']);

                        $folder_route['children'][] = $vs_route;

                    }
                }

            } else {

                if (isset($tmproutes['main'][$folder])) {
                    foreach ($tmproutes['main'][$folder] as $key => $model) {

                        if (empty($model['children'])) {
                            continue;
                        }

                        $vs_route = $this->addRouteToList($key, $f_folder_path . '/' . $key, 'router_view');
                        $vs_route['children'] = array_values($model['children']);

                        $folder_route['children'][] = $vs_route;
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

    public function addRouteToList($path, $name, $component, $no_path = false, $search_path = '', $meta_path = [])
    {

        $meta = ['breadcrumb' => true, 'middlewareAuth' => true];

        if ($search_path != '') {
            $meta = ['breadcrumb' => true, 'middlewareAuth' => true, 'search_path' => Str::lower($search_path)];
        }

        if (!empty($meta_path)) {
            $meta['path'] = $meta_path;
        }

        return [
            'path' => $no_path ? '' : Str::lower($path),
            'meta' => $meta,
            'name' => $no_path ? Str::lower(str_replace('/', '.', $name)) . '.default' : Str::lower(str_replace('/', '.', $name)),
            'component' => Str::lower($component),
        ];
    }
}
