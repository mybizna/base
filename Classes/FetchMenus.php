<?php

namespace Modules\Base\Classes;

class FetchMenus
{

    public $menus = [];
    public $paths = [];

    public function __construct()
    {
        $groups = (is_file(base_path('../readme.txt'))) ? ['Modules/*', '../../*/Modules/*'] : ['Modules/*'];
        foreach ($groups as $key => $group) {
            $this->paths = array_merge($this->paths, glob(base_path($group)));
        }
    }

    public function fetchMenus()
    {
        $column = 'position';

        foreach ($this->paths as $key => $path) {
            $file_names = ['menu', 'menus'];

            foreach ($file_names as $key => $file_name) {
                $menu_file = $path . DIRECTORY_SEPARATOR . $file_name . '.php';

                if (file_exists($menu_file)) {
                    include_once $menu_file;
                }
            }
        }

        // Reorder Menu
        usort($this->menus, function ($a, $b) use ($column) {
            if (isset($a[$column]) && isset($b[$column])) {
                return $a[$column] <=> $b[$column];
            }
            return -1;
        });

        // Reorder SubMenu
        foreach ($this->menus as $module => $menu) {

            $tmp_menus = $this->menus[$module]['menus'];

            usort($tmp_menus, function ($a, $b) use ($column) {
                if (isset($a[$column]) && isset($b[$column])) {
                    return $a[$column] <=> $b[$column];
                }
                return -1;
            });

            foreach ($tmp_menus as $key => $tmp_submenu) {
                $tmp_submenu_list = $tmp_submenu['list'];

                unset($tmp_menus[$key]);
                $tmp_menus[$tmp_submenu['key']] = $tmp_submenu;

                usort($tmp_submenu_list, function ($a, $b) use ($column) {
                    if (isset($a[$column]) && isset($b[$column])) {
                        return $a[$column] <=> $b[$column];
                    }
                    return -1;
                });

                $tmp_menus[$tmp_submenu['key']]['list'] = [];
                $tmp_menus[$tmp_submenu['key']]['list'] = $tmp_submenu_list;
            }

            $this->menus[$module]['menus'] = [];
            $this->menus[$module]['menus'] = $tmp_menus;
        }

        return $this->menus;
    }

    public function add_module_info($module, $data)
    {
        if (!array_key_exists($module, $this->menus)) {
            $this->menus[$module] = ['menus' => []];
        }

        $data['key'] = $module;
        $data['position'] = (isset($data['position'])) ? $data['position'] : 5;

        $this->menus[$module] = array_merge($this->menus[$module], $data);
    }

    public function add_menu($module, $key, $title, $path, $icon, $position)
    {
        $this->menus[$module]['menus'][$key]['title'] = $title;
        $this->menus[$module]['menus'][$key]['key'] = $key;
        $this->menus[$module]['menus'][$key]['path'] = $path;
        $this->menus[$module]['menus'][$key]['position'] = $position;
        $this->menus[$module]['menus'][$key]['icon'] = $icon;

        if (!isset($this->menus[$module]['menus'][$key]['list'])) {
            $this->menus[$module]['menus'][$key]['list'] = [];
        }
    }

    public function add_submenu($module, $key, $title, $path, $position)
    {
        $this->menus[$module]['menus'][$key]['list'][] = [
            'title' => $title,
            'key' => $key,
            'path' => $path,
            'position' => $position,
        ];
    }

    private function loadDefaultMenus($base_path)
    {

        $file_names = ['menu', 'menus'];

        foreach ($file_names as $key => $file_name) {
            $menu_file = $base_path . DIRECTORY_SEPARATOR . $file_name . '.php';

            if (file_exists($menu_file)) {
                include_once $menu_file;
            }
        }
    }
}
