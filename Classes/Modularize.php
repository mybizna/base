<?php

namespace Modules\Base\Classes;

use Illuminate\Support\Str;

class Modularize
{
    public $module;
    public $model;
    public $menus = [];
    public $layouts = [];

    function __construct($module = '', $model = '')
    {
        $this->module = $module;
        $this->model = $model;
    }

    public function getAllRecords($args)
    {
        $classname = $this->getClassName($this->module, $this->model);

        if ($classname) {
            if (method_exists($classname, 'getAllRecords')) {
                $result = $classname->getAllRecords($args);
            }
        } else {
            $result['message'] = 'No Model Found with name ' . $this->module . '-' . $this->model;
        }

        return $result;
    }

    public function getRecord($id, $args = [])
    {
        $classname = $this->getClassName($this->module, $this->model);

        if ($classname) {
            if (method_exists($classname, 'getRecord')) {
                $result = $classname->getRecord($id, $args);
            }
        } else {
            $result['message'] = 'No Model Found with name ' . $this->module . '-' . $this->model;
        }

        return $result;
    }

    public function getRecordSelect($args)
    {
        $classname = $this->getClassName($this->module, $this->model);

        if ($classname) {
            if (method_exists($classname, 'getRecordSelect')) {
                $result = $classname->getRecordSelect($args);
            }
        } else {
            $result['message'] = 'No Model Found with name ' . $this->module . '-' . $this->model;
        }

        return $result;
    }


    public function createRecord($args = [])
    {
        $classname = $this->getClassName($this->module, $this->model);

        if ($classname) {
            if (method_exists($classname, 'createRecord')) {
                $result = $classname->createRecord($args);
            }
        } else {
            $result['message'] = 'No Model Found with name ' . $this->module . '-' . $this->model;
        }

        return $result;
    }

    public function updateRecord($args = [])
    {
        $classname = $this->getClassName($this->module, $this->model);

        if ($classname) {
            if (method_exists($classname, 'updateRecord')) {
                $result = $classname->updateRecord($args);
            }
        } else {
            $result['message'] = 'No Model Found with name ' . $this->module . '-' . $this->model;
        }

        return $result;
    }

    public function deleteRecord($id)
    {
        $classname = $this->getClassName($this->module, $this->model);

        if ($classname) {
            if (method_exists($classname, 'deleteRecord')) {
                $result = $classname->deleteRecord($id);
            }
        } else {
            $result['message'] = 'No Model Found with name ' . $this->module . '-' . $this->model;
        }

        return $result;
    }

    //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
    //Fetching Routes
    public function fetchRoutes()
    {
        $fetchroutes = new FetchRoutes();

        return $fetchroutes->fetchRoutes();
    }

    //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
    //Discover Modules
    public function discoverModules()
    {
        $discover_modules = new DiscoverModules();

        return $discover_modules->discoverModules();
    }

    //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
    //Fetching Menu
    public function fetchMenus()
    {
        $fetchmenus = new FetchMenus();

        return $fetchmenus->fetchMenus();
    }

    //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
    //Fetching Setting
    public function fetchSettings()
    {
        $fetchsettings = new FetchSettings();

        return $fetchsettings->fetchSettings();
    }
    //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
    //General Classes
    private function getClassName()
    {
        $classname = 'Modules\\' . ucfirst($this->module) . '\Entities\\' . ucfirst(Str::camel($this->model));

        return (class_exists($classname)) ? new $classname() : false;
    }
}
