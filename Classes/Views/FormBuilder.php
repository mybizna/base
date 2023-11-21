<?php

namespace Modules\Base\Classes\Views;

class FormBuilder
{

    //intialize fields as an object
    public $fields = [];

    //initialize name as an empty string
    public $name = '';

    /**
     * Create a new Form instance.
     *
     * @return void
     */

    public function __construct($fields = [])
    {
        //check if fields is empty array
        if (!empty($fields)) {
            $this->fields = $fields;
        }

    }

    /**
     * Set the name of the field
     *
     * @param string $name
     */
    public function name($name): FormBuilder
    {
        $common = new Common();

        $label = $common->getLabel($name);

        $this->fields[$name] = new \stdClass();
        $this->fields[$name]->name = $name;
        $this->fields[$name]->label = $label;

        $this->name = $name;
        return $this;
    }

    /**
     * Set the type of the field
     */

    public function type($type): FormBuilder
    {
        $this->fields[$this->name]->type = $type;
        return $this;
    }

    /**
     * Set the label of the field
     *
     * @param string $label
     *
     */
    public function label($label): FormBuilder
    {
        $this->fields[$this->name]->label = $label;
        return $this;
    }

    /**
     * Set the group of the field
     *
     * @param string $group
     *
     */
    public function group($group): FormBuilder
    {
        $this->fields[$this->name]->group = $group;
        return $this;
    }

    /**
     * Set the ordering of the field
     *
     * @param bool $ordering
     *
     */
    public function ordering($ordering): FormBuilder
    {
        $this->fields[$this->name]->ordering = $ordering;
        return $this;
    }

    /**
     * Set the table of the field
     *
     * @param string $table
     *
     * */
    public function table($table): FormBuilder
    {
        $this->fields[$this->name]->table = $table;
        return $this;
    }

    /**
     * Set the options of the field
     *
     * @param array<string|int, string> $options
     */
    public function options($options): FormBuilder
    {
        $this->fields[$this->name]->options = $options;
        return $this;
    }

    /**
     * Set the default value of the field
     *
     * @param string $default
     */
    function default($default): FormBuilder{
        $this->fields[$this->name]->default = $default;
        return $this;
    }
    /**
     * Remove or disable the field
     *
     * @param string $name
     *
     */
    public function remove($name): FormBuilder
    {
        unset($this->fields[$name]);
        return $this;
    }
}
