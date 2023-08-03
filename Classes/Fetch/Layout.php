<?php

namespace Modules\Base\Classes\Fetch;

class Layout
{
    public $paths = [];

    public function __construct()
    {
        $groups = (is_file(base_path('../readme.txt'))) ? ['Modules/*', '../../*/Modules/*'] : ['Modules/*'];
        foreach ($groups as $key => $group) {
            $this->paths = array_merge($this->paths, glob(base_path($group)));
        }

    }

    public function fetchLayout($params)
    {
        $fields = [];
        $class_name = $this->getClassName($params['module'], $params['model']);

        $action = $params['action'];

        if ($class_name) {

            switch ($action) {
                case 'list':
                    $listTable = $class_name->listTable($params);
                    $fields = $listTable->fields;
                    break;
                case 'create':
                case 'edit':
                case 'form':
                    $listForm = $class_name->listForm($params);
                    $fields = $listForm->fields;
                    break;
                case 'filter':
                    $listFilter = $class_name->filter($params);
                    $fields = $listFilter->fields;
                    break;
            }

            $result = $this->prepareResult('Layout Fetched Successfully', false, $fields);

        } else {
            $result = $this->prepareResult('No Model Found with name ' . $params['module'] . '-' . $params['model'], true);
        }

        return $result;
    }

    public function prepareResult($message, $error = false, $data = [])
    {
        return [
            'error' => $error,
            'message' => $message,
            'layout' => $data,
        ];
    }

    /**
     * Get Class Name
     *
     * @param string $module
     *
     * @return mixed
     */
    private function getClassName($module, $model): mixed
    {
        $module = ucfirst(strtolower($module));
        $model = ucfirst(strtolower($model));

        $classname = 'Modules\\' . $module . '\Entities\\' . $model;

        if (class_exists($classname)) {
            return new $classname();
        } else {
            throw new \Exception("class $classname not found.", 1);

        };
    }

}
