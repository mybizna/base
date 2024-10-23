<?php

namespace Modules\Base\Classes\Fetch;

/**
 * Class Positions
 *
 * The class is used to fetch the positions of the module
 *
 * @package Modules\Base\Classes\Fetch
 */
class Positions
{

    /**
     * Positions
     *
     * @var array
     */
    public $positions = [];

    /**
     * Paths to search for modules
     *
     * @var array
     */
    public $paths = [];

    /**
     * Positions Constructor
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
     * Fetch Positions
     *
     * @return Array
     */
    public function fetchPositions(): array
    {
        // Fetch the widgets
        foreach ($this->paths as $key => $path) {
            $file_names = ['widget', 'widgets'];

            foreach ($file_names as $key => $file_name) {
                $position_file = $path . DIRECTORY_SEPARATOR . $file_name . '.php';

                if (file_exists($position_file)) {
                    include_once $position_file;
                }
            }
        }

        //print_r($this->positions);exit();

        // Sort the positions
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

    /**
     * Add Widget
     *
     * @param string $module
     * @param string $position
     * @param string $title
     * @param string $path
     * @param int $ordering
     * @param bool $has_wrapper
     *
     * @return void
     */
    public function add_widget($module, $position, $title, $path, $ordering, $has_wrapper = true): void
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
