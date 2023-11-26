<?php

namespace Modules\Base\Classes\Fetch;

use Illuminate\Support\Str;

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
        $fields = $layout = [];
        $class_name = $this->getClassName($params['module'], $params['model']);

        $action = $params['action'];

        if ($class_name) {

            switch ($action) {
                case 'list':

                    $schema = $class_name->getStructureAndFields(true);

                    foreach ($schema['structure']['table'] as $key => $field) {
                        $fields[] = $field;

                        $field_arr = $schema['fields'][$field];
                        $field_arr['label'] = $this->getLabel($field);

                        if (array_key_exists("relation", $field_arr) && $field_arr['relation'][0] != ['users']) {

                            $foreign_fields = [];
                            $relation = $this->getRelation($field_arr, $action);
                            foreach ($relation['fields'] as $key => $rfield) {
                                $foreign_fields[] = $field_arr['name'] . '__' . implode('_', $field_arr['relation']) . '__' . $rfield;
                            }
                            $schema['fields'][$field]['foreign_fields'] = $foreign_fields;
                        }

                        $schema['fields'][$field]['label'] = ucwords(str_replace('_', ' ', $schema['fields'][$field]['name'] ));

                        $layout[$field] = $schema['fields'][$field];

                    }

                    break;

                case 'create':
                case 'edit':
                case 'form':
                    $schema = $class_name->getStructureAndFields(true);

                    $layout = $schema['structure']['form'];
                    foreach ($schema['structure']['form'] as $key => $row) {

                        $layout[$key]['fields'] = [];
                        foreach ($row['fields'] as $tmpkey => $field) {
                            if (isset($schema['fields'][$field])) {
                                $fields[] = $field;

                                $field_arr = $schema['fields'][$field];
                                $field_arr['label'] = $this->getLabel($field);

                                switch ($field_arr['html']) {
                                    case 'recordpicker':
                                        $relation = $this->getRelation($field_arr, $action);
                                        $field_arr['picker'] = $relation;
                                        break;
                                    case 'amount':
                                        $field_arr['html'] = 'text';
                                        break;
                                    case 'select':
                                    case 'radio':
                                    case 'checkbox':
                                        if (!isset($field_arr['options'])) {
                                            $field_arr['options'] = (isset($field_arr['allowed'])) ? $field_arr['allowed'] : [];
                                        }
                                        break;

                                    default:
                                        break;
                                }

                                $layout[$key]['fields'][] = $field_arr;
                            }
                        }

                    }

                    break;

                case 'filter':
                    $listFilter = $class_name->filter($params);
                    $fields = $listFilter->fields;
                    break;
            }

            $result = $this->prepareResult('Layout Fetched Successfully', false, $fields, $layout);

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

    public function getRelation($field, $action): array
    {
        $module = $field['relation'][0];
        $model = $field['relation'][1] ?? $module;

        $relation = $this->getClassName($module, $model);

        $rec_names = $relation->getRecNames();

        $fields = $rec_names;

        $template = trim(vsprintf(str_repeat('[%s] ', count($rec_names)), $rec_names), ' ');

        if ((count($rec_names) != count($rec_names, COUNT_RECURSIVE))) {
            $fields = $rec_names['fields'];
            $template = $rec_names['template'];
        }
        $action_do = $action . 'ing';

        if ($action == 'create') {
            $action_do = 'creating';
        }

        $relation = [
            'path_title' => $model . ' ' . $action_do,
            'path_param' => [$module, $model],
            'fields' => $fields,
            'template' => $template,
        ];

        return $relation;
    }

    public function prepareResult($message, $error = false, $fields = [], $layout = [])
    {
        return [
            'error' => $error,
            'message' => $message,
            'fields' => $fields,
            'layout' => $layout,
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
        $model = ucfirst(Str::camel($model));

        $classname = 'Modules\\' . $module . '\Entities\\' . $model;

        if (class_exists($classname)) {
            return new $classname();
        } else {
            throw new \Exception("class $classname not found.", 1);

        };
    }

}
