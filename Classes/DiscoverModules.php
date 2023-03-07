<?php

namespace Modules\Base\Classes;

class DiscoverModules
{
    public $paths = [];

    public function __construct()
    {
        $groups = (is_file(base_path('../readme.txt'))) ? [base_path('Modules/*'), base_path('../../*/Modules/*')] : [base_path('Modules/*')];
        foreach ($groups as $key => $group) {
            $this->paths = array_merge($this->paths, glob(base_path($group)));
        }

    }

    public function discoverModules()
    {
        foreach ($this->paths as $key => $path) {
            $path_arr = array_reverse(explode('/', $path));
            $module_name = $path_arr[0];

        }

        return;
    }
}
