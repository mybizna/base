<?php

namespace Modules\Base\Classes\Fetch;

use Illuminate\Support\Facades\File;

/**
 * Vue class
 *
 * This class is used to fetch the vue files
 *
 * @package Modules\Base\Classes\Fetch
 */
class Vue
{
    /**
     * Paths
     *
     * @var array
     */
    public $paths = [];

    /**
     * Vue constructor.
     *
     * The constructor is used to fetch the paths
     */
    public function __construct()
    {
        $groups = (is_file(base_path('../readme.txt'))) ? ['Modules/*', '../../*/Modules/*'] : ['Modules/*'];

        foreach ($groups as $key => $group) {
            $this->paths = array_merge($this->paths, glob(base_path($group)));
        }

    }

    /**
     * Fetch Vue
     *
     * The function is used to fetch the vue file
     *
     * @param array $current_uri
     *
     * @return array
     */
    public function fetchVue($current_uri)
    {
        $DS = DIRECTORY_SEPARATOR;
        $contents = 'Vue File not found.';
        $status = 404;
        $module_plugins = [];
        $vue_file = '';
        $vue_file_status = false;

        unset($current_uri[0]);

        // Loop through the paths
        foreach ($this->paths as $file) {
            // Check if the file is a directory
            if (is_dir($file)) {
                $override_folders = ['override', 'overrides'];

                foreach ($override_folders as $key => $folder) {
                    $vue_file = $file . '/Resources/vue/' . $folder . '/' . implode('/', $current_uri);

                    // Check if the file is a vue file
                    if (is_file($vue_file)) {
                        $vue_file_status = true;
                        break;
                    }
                }

                if ($vue_file_status) {
                    break;
                }
            }
        }

        // Check if vue file is not found
        if (!$vue_file_status) {
            if ($current_uri[1] == 'templates') {
                $suffix_url = implode($DS, $current_uri);
                $vue_file = base_path() . $DS . $suffix_url;

                // check if vue_file exists
                if ($vue_file != '' && File::isFile($vue_file)) {
                    $vue_file_status = true;
                } else {
                    // get all directories on the path base_path('templates')
                    $dirs = glob(base_path('templates') . '/*', GLOB_ONLYDIR);

                    foreach ($dirs as $key => $dir) {
                        $dir_arr = explode($DS, $dir);
                        $dir_name = end($dir_arr);

                        // continue if dir name is default
                        if ($dir_name == 'default') {
                            continue;
                        }

                        $suffix_url = str_replace('default', $dir_name, $suffix_url);
                        $vue_file = base_path() . $DS . $suffix_url;

                        // check if vue_file exists
                        if ($vue_file != '' && File::isFile($vue_file)) {
                            $vue_file_status = true;
                            break;
                        }
                    }
                }
            } else {
                $module = $current_uri[1];
                unset($current_uri[1]);
                
                $vue_file = base_path() . $DS . 'Modules' . $DS . ucfirst($module) . $DS . 'Resources' . $DS . 'vue' . $DS . implode($DS, $current_uri);

                if ($vue_file != '' && File::isFile($vue_file)) {
                    $vue_file_status = true;
                }

            }
        }

        if ($vue_file_status) {
            $contents = file_get_contents($vue_file);
            $status = 200;
        } else {
            $contents = 'Vue File not found.';
            $status = 404;
        }

        return [$contents, $status];
    }

}
