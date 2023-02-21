<?php

namespace Modules\Base\Classes;

use Illuminate\Support\Facades\File;

class FetchVue
{

    public function fetchVue($current_uri)
    {

        $DS = DIRECTORY_SEPARATOR;
        $contents = 'Vue File not found.';
        $status = 404;
        $module_plugins = [];

        $module = $current_uri[1];

        unset($current_uri[0]);
        unset($current_uri[1]);

        $module_path = realpath(base_path()) . $DS . 'Modules' . $DS . ucfirst($module);

        $vue_file = '';
        if (File::isDirectory($module_path)) {
            $vue_file = $module_path . $DS . 'views' . $DS . implode($DS, $current_uri);
        } elseif ($module_plugins[$module]) {
            $vue_file = $module_plugins[$module] . $DS . 'views' . $DS . implode($DS, $current_uri);
        }

        if ($vue_file != '' && File::isFile($vue_file)) {
            $contents = file_get_contents($vue_file);
            $status = 200;
        }

        return [$contents, $status];
    }

}
