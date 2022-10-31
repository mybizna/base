<?php

namespace Modules\Base\Classes;

use Illuminate\Support\Str;
use Modules\Core\Entities\Setting as DBSetting;

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
                    $module_name_slug = Str::lower($module_name);

                    

                    $file_names = ['setting', 'settings'];

                    foreach ($file_names as $key => $file_name) {
                        $setting_file = $modules_path . $DS . $module_name . $DS . $file_name . '.php';
                        if (file_exists($setting_file)) {
                            $this->add_module_info($module_name_slug, [
                                'title' => $module_name,
                                'description' => $module_name,
                            ]);

                            $settings = require $setting_file;

                            foreach ($settings as $setting_name => $setting) {
                                $category = (isset($setting['category']) && $setting['category'] != '')
                                ? $setting['category']
                                : 'Main';
                                $category_slug = Str::snake(Str::lower($category));

                                $category_position = ($category_slug == 'main') ? 5 : 1;
                                $field_position = (isset($setting['position']) && $setting['position'] != '')
                                ? $setting['position']
                                : 5;

                                $this->add_setting_category($module_name_slug, $category_slug, $category, $category_position);

                                $this->add_setting($module_name_slug, $category_slug, $setting_name, $field_position, $setting);
                            }
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

    public function add_setting_category($module, $key, $title, $position, $params = [])
    {
        if (!array_key_exists($key,  $this->settings[$module]['settings'])) {
            $this->settings[$module]['settings'][$key] = [
                'title' => $title,
                'position' => $position,
                'params' => $params,
                'list' => [],
            ];
        }
    }

    public function add_setting($module, $key, $name, $position, $params)
    {
        $setting = DBSetting::where('module', $module)->where('name', $name)->first();

        if ($setting) {
            $params['value'] = $setting['value'];
        }

        $this->settings[$module]['settings'][$key]['list'][] = [
            'name' => $name,
            'position' => $position,
            'params' => $params,
        ]; 
    }
}
