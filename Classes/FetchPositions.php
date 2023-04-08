<?php

namespace Modules\Base\Classes;

class FetchPositions
{

    public $positions = [];
    public $paths = [];

    public function __construct()
    {
        $groups = (is_file(base_path('../readme.txt'))) ? ['Modules/*', '../../*/Modules/*'] : ['Modules/*'];
        foreach ($groups as $key => $group) {
            $this->paths = array_merge($this->paths, glob(base_path($group)));
        }
    }

    public function fetchPositions()
    {
        foreach ($this->paths as $key => $path) {
            $file_names = ['widget', 'widgets'];

            foreach ($file_names as $key => $file_name) {
                $position_file = $path . DIRECTORY_SEPARATOR . $file_name . '.php';

                if (file_exists($position_file)) {
                    include_once $position_file;
                }
            }
        }

        $column = 'ordering';
        foreach ($this->positions as $key => $tmp_position) {

            usort($tmp_position, function ($a, $b) use ($column) {
                if (isset($a[$column]) && isset($b[$column])) {
                    return $a[$column] <=> $b[$column];
                }
                return -1;
            });

            $this->positions[$key] = $tmp_position;
        }

        return $this->positions;
    }

    public function add_widget($module, $position, $title, $path, $ordering, $has_wrapper = true)
    {
        $this->positions[$position][] = [
            'module' => $module,
            'title' => $title,
            'path' => $path,
            'ordering' => $ordering,
            'has_wrapper' => $has_wrapper,
        ];
    }
}
