<?php

namespace Modules\Base\Classes\Fetch;

use Illuminate\Support\Str;

/**
 * Class Layout
 *
 * The class is used to fetch the layout of the module
 *
 * @package Modules\Base\Classes\Fetch
 */
class Layout
{
    /**
     * Paths to search for modules
     *
     * @var array
     */
    public $paths = [];

    /**
     * Layout Constructor
     */
    public function __construct()
    {
        $groups = (is_file(base_path('../readme.txt'))) ? ['Modules/*', '../../*/Modules/*'] : ['Modules/*'];
        foreach ($groups as $key => $group) {
            $this->paths = array_merge($this->paths, glob(base_path($group)));
        }

    }

    /**
     * Fetch Layout
     *
     * @param Array $params
     *
     * @return Array
     */
    public function fetchLayout($params)
    {
        $fields = $layout = $filter = [];
        $class_name = $this->getClassName($params['module'], $params['model']);

        $action = $params['action'];

        if ($class_name) {

            switch ($action) {
                case 'list':

                    // Get the schema of the model
                    $schema = $class_name->getStructureAndFields(true);

                    // Get the fields to be displayed in the table
                    foreach ($schema['structure']['table'] as $key => $field) {
                        $fields[] = $field;

                        $field_arr = $schema['fields'][$field];
                        $field_arr['label'] = $this->getLabel($field);
                        $field_arr['placeholder'] = $field_arr['label'];

                        // Check if the field is a relation
                        if (array_key_exists("relation", $field_arr) && $field_arr['relation'][0] != ['users']) {

                            $foreign_fields = [];
                            $relation = $this->getRelation($field_arr, $action);
                            foreach ($relation['fields'] as $key => $rfield) {
                                $foreign_fields[] = $field_arr['name'] . '__' . implode('_', $field_arr['relation']) . '__' . $rfield;
                            }
                            $schema['fields'][$field]['foreign_fields'] = $foreign_fields;
                        }

                        $schema['fields'][$field]['label'] = ucwords(str_replace('_', ' ', $schema['fields'][$field]['name']));

                        $layout[$field] = $schema['fields'][$field];

                    }

                    // Get the fields to be displayed in the filter
                    foreach ($schema['structure']['filter'] as $key => $field) {

                        if (isset($schema['fields'][$field]) && !in_array($field, ['id']) && !in_array($schema['fields'][$field]['html'], ['recordpicker'])) {

                            $field_arr = $this->prepareFormField($schema, $field, $action);

                            $filter[] = $field_arr;
                        }

                    }

                    break;

                case 'create':
                case 'edit':
                case 'form':
                    // Get the schema of the model
                    $schema = $class_name->getStructureAndFields(true);

                    $layout = $schema['structure']['form'];

                    // Get the fields to be displayed in the form
                    foreach ($schema['structure']['form'] as $key => $row) {

                        $layout[$key]['fields'] = [];

                        // Get the fields to be displayed in the form
                        if (isset($row['fields']) && is_array($row['fields'])) {
                            foreach ($row['fields'] as $tmpkey => $field) {
                                if (isset($schema['fields'][$field])) {
                                    $fields[] = $field;

                                    $field_arr = $this->prepareFormField($schema, $field, $action);

                                    $layout[$key]['fields'][] = $field_arr;
                                }
                            }
                        }

                    }

                    break;

            }

            $result = $this->prepareResult('Layout Fetched Successfully', false, $fields, $layout, $filter);

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

        $action_do = $action . 'ing';

        if ($action == 'create') {
            $action_do = 'creating';
        }

        if ($module == 'users') {
            $relation = [
                'path_title' => 'User ' . $action_do,
                'path_param' => ["Users", "Users"],
                'fields' => ['name', 'email', 'username', 'phone'],
                'template' => '[name] [email] [username] [phone]',
            ];
            return $relation;
        }

        $rec_names = $relation->getRecNames();

        $fields = $rec_names;

        $template = trim(vsprintf(str_repeat('[%s] ', count($rec_names)), $rec_names), ' ');

        if ((count($rec_names) != count($rec_names, COUNT_RECURSIVE))) {
            $fields = $rec_names['fields'];
            $template = $rec_names['template'];
        }

        $relation = [
            'path_title' => $model . ' ' . $action_do,
            'path_param' => [$module, $model],
            'fields' => $fields,
            'template' => $template,
        ];

        return $relation;
    }

    /**
     * Prepare Form Field
     *
     * @param Array $schema
     * @param String $field
     * @param String $action
     *
     * @return Array
     */
    public function prepareFormField($schema, $field, $action): array
    {

        $field_arr = $schema['fields'][$field];
        $field_arr['label'] = $this->getLabel($field);
        $field_arr['placeholder'] = 'Enter ' . $field_arr['label'];
        $field_arr['label'] = $field_arr['label'] . ': ';

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

        return $field_arr;
    }

    /**
     * Prepare Result
     *
     * @param String $message
     * @param Boolean $error
     * @param Array $fields
     * @param Array $layout
     * @param Array $filter
     *
     * @return Array
     */
    public function prepareResult($message, $error = false, $fields = [], $layout = [], $filter = []): array
    {
        return [
            'error' => $error,
            'message' => $message,
            'fields' => $fields,
            'filter' => $filter,
            'layout' => $layout,
        ];
    }

    /**
     * Set the name of the field
     *
     * @param string $name
     *
     * @return string
     */
    public function getLabel($name): string
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

        if ($module == 'Users') {
            return false;
        }

        $classname = 'Modules\\' . $module . '\Entities\\' . $model;

        if (class_exists($classname)) {
            return new $classname();
        } else {
            throw new \Exception("class $classname not found.", 1);

        };
    }

}
