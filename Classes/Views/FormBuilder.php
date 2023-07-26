<?php

namespace Modules\Base\Classes\Views;

class FormBuilder
{

    //intialize fields as an object
    public $fields;

    /**
     * Create a new Form instance.
     *
     * @return void
     */

    public function __construct($fields)
    {
        //convert array to object
        if (is_array($fields)) {
            $fields = (object) $fields;
        } else {
            $fields = new \stdClass();
        }

        $this->fields = $fields;
    }

    /**
     * Set the name of the field
     * @param string $name
     */
    public function name($name)
    {
        $this->fields[$name] = new \stdClass();
        $this->fields[$name]->name = $name;
        return $this;
    }

    /**
     * Set the type of the field
     */

    public function type($type){
        $this->fields[$this->fields->name]->type = $type;
        return $this;
    }

    /** 
     * Set the label of the field
     * @param string $label
     *
     */
    public function label($label){
        $this->fields[$this->fields->name]->label = $label;
        return $this;
    }

    /** 
     * Set the group of the field
     * @param string $group
     *
     */
    public function group($group){
        $this->fields[$this->fields->name]->group = $group;
        return $this;
    }

    /** 
     * Set the ordering of the field
     * @param string $ordering
     *
     */
    public function ordering($ordering){
        $this->fields[$this->fields->name]->ordering = $ordering;
        return $this;
    }

    /**
     * Set the table of the field
     * @param string $table
     *  
     * */ 
    public function table($table){
        $this->fields[$this->fields->name]->table = $table;
        return $this;
    }

    /**
     * Set the options of the field
     * @param string $options
     */
    public function options($options){
        $this->fields[$this->fields->name]->options = $options;
        return $this;
    }

    /**
     * Set the default value of the field
     * @param string $default
     */
    public function default($default){
        $this->fields[$this->fields->name]->default = $default;
        return $this;
    }
    /**
     * Remove or disable the field
     * @param string $name
     *
     */
    public function remove($name){
        unset($this->fields[$name]);
        return $this;
    }
}
