<?php

namespace Modules\Base\Classes;

use Illuminate\Support\Facades\File;

class FetchVue
{
    public $paths = [];

    public function __construct()
    {
        $groups = (is_file(base_path('../readme.txt'))) ? ['Modules/*', '../../*/Modules/*'] : ['Modules/*'];
        foreach ($groups as $key => $group) {
            $this->paths = array_merge($this->paths, glob(base_path($group)));
        }

    }

    public function fetchVue($current_uri)
    {

        $DS = DIRECTORY_SEPARATOR;
        $contents = 'Vue File not found.';
        $status = 404;
        $module_plugins = [];

        $module = $current_uri[1];

        unset($current_uri[0]);
        unset($current_uri[1]);

        foreach ($this->paths as $key => $path) {

            $path_arr = array_reverse(explode('/', $path));
            $module_name = $path_arr[0];

            if (strtoupper($module_name) == strtoupper($module)) {
                $vue_file = $path . $DS . 'views' . $DS . implode($DS, $current_uri);

                if ($vue_file != '' && File::isFile($vue_file)) {
                    $contents = file_get_contents($vue_file);
                    $status = 200;
                }

            }

        }

        return [$contents, $status];
    }

}
