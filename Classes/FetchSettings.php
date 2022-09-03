<?php

namespace Modules\Base\Classes;

use Illuminate\Support\Str;

class FetchSettings
{

    public $settings = [];

    public function fetchSettings()
    {

        $DS = DIRECTORY_SEPARATOR;


        $modules_path = realpath(base_path()) . $DS . 'Modules';

        if (is_dir($modules_path)) {
            $dir = new \DirectoryIterator($modules_path);

            foreach ($dir as $fileinfo) {
                if (!$fileinfo->isDot() && $fileinfo->isDir()) {
                    $module_name = $fileinfo->getFilename();

                    $file_names = ['setting', 'settings'];

                    foreach ($file_names as $key => $file_name) {
                        $setting_file = $modules_path .  $DS . $module_name .  $DS . $file_name . '.php';
                        if (file_exists($setting_file)) {
                            include_once $setting_file;
                        }
                    }
                }
            }
        }

        return $this->settings;
    }


    public function add_module_info($module, $data)
    {
        if (!array_key_exists($module, $this->settings)) {
            $this->settings[$module] = ['settings' => []];
        }

        $this->settings[$module] = array_merge($this->settings[$module], $data);
    }

    public function add_setting_category($module, $key, $title, $path, $icon, $position)
    {
        $this->settings[$module]['settings'][$key] = [
            'title' => $title,
            'path' => $path,
            'position' => $position,
            'icon' => $icon,
            'list' => []
        ];
    }

    public function add_setting($module, $key, $title, $path, $position)
    {
        $this->settings[$module]['settings'][$key]['list'][] = [
            'title' => $title,
            'path' => $path,
            'position' => $position,
        ];
    }
}
