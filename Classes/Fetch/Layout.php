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

                    $schema = $class_name->getStructureAndFields(true);

                    $list_fields = [];
                    foreach ($schema['structure']['table'] as $key => $table) {
                        $field = $schema['fields'][$table];
                        $field['label'] = $this->getLabel($table);
                        $list_fields[$table] = $field;
                    }

                    $fields = $this->addForeignFields($list_fields);

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

    /**
     * Function for adding foreign key to the table listing
     *
     * @param Array $fields
     *
     * @return Array
     */

    public function addForeignFields($fields): array
    {
        foreach ($fields as $field_name => $field) {

            $name_prefix = $field['name'] . '__';

            if (isset($field['relation'])) {
                $module = $field['relation'][0];
                $model = $field['relation'][1] ?? $module;

                $name_prefix .= implode('_', $field['relation']) . '__';

                $relation = $this->getClassName($module, $model);

                $rec_names = $relation->getRecNames();

                $foreign_fields = [];
                foreach ($rec_names as $key => $rec_name) {
                    $foreign_fields[] = $name_prefix . $rec_name;

                }

                $fields[$field_name]['foreign_fields'] = $foreign_fields;

            }
            # code...
        }
        return $fields;
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
     * Set the name of the field
     *
     * @param string $name
     */
    public function getLabel($name)
    {
        if ($name == 'id') {
            return 'ID';
        } else {
            $name = str_replace('_id', '', $name);
            $name = str_replace('_', ' ', $name);
            $name = ucwords($name);
        }

        return $name;
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
