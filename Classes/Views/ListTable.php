<?php

namespace Modules\Base\Classes\Views;

class ListTable
{

    //intialize fields as an object
    public $fields = [];

    //initialize name as an empty string
    public $name = '';

    /**
     * Create a new ListTable instance.
     *
     * @return void
     */

    public function __construct($fields = null)
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
    public function name($name): ListTable
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

    public function type($type): ListTable
    {
        $this->fields[$this->name]->type = $type;
        return $this;
    }

    /**
     * Set the label of the field
     *
     * @param
     * @param
     * @param string $label
     *
     */
    public function label($label): ListTable
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
    public function group($group): ListTable
    {
        $this->fields[$this->name]->group = $group;
        return $this;
    }

    /**
     * Set the color of the field
     */

    public function color($color): ListTable
    {
        $this->fields[$this->name]->color = $color;

        return $this;
    }

    /**
     * Set the ordering of the field
     *
     * @param bool $ordering
     *
     */
    public function ordering($ordering): ListTable
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
    public function table($table): ListTable
    {
        $this->fields[$this->name]->table = $table;
        return $this;
    }

    /**
     * Set the recnames of the field
     */

    public function recnames($recnames): ListTable
    {
        $this->fields[$this->name]->recnames = $recnames;
        return $this;
    }

    /**
     * Set the options of the field
     *
     * @param array<string|int, string> $options
     *
     * */
    public function options($options): ListTable
    {
        $this->fields[$this->name]->options = $options;
        return $this;
    }

    /**
     * Set the default value of the field
     *
     * @param string $default
     *
     * */
    function default($default): ListTable {
        $this->fields[$this->name]->default = $default;
        return $this;
    }

    /**
     * Remove or disable the field
     */

    public function remove(): ListTable
    {
        unset($this->fields[$this->name]);
        return $this;
    }

}
