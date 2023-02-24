<?php

namespace Modules\Base\Classes;

use Illuminate\Support\Str;

class Modularize
{
    public $module;
    public $model;
    public $menus = [];
    public $layouts = [];

    public function __construct($module = '', $model = '')
    {
        $this->module = $module;
        $this->model = $model;
    }

    public function getAllRecords($args)
    {
        $classname = $this->getClassName($this->module, $this->model);

        if ($classname) {
            if (method_exists($classname, 'getAllRecords')) {
                $classname->module = $this->module;
                $classname->model = $this->model;
                $result = $classname->getAllRecords($args);
            }
        } else {
            $result = $this->prepareResult('No Model Found with name ' . $this->module . '-' . $this->model, true);

        }

        return $result;
    }

    public function getRecord($id, $args = [])
    {
        $classname = $this->getClassName($this->module, $this->model);

        if ($classname) {
            if (method_exists($classname, 'getRecord')) {
                $classname->module = $this->module;
                $classname->model = $this->model;
                $result = $classname->getRecord($id, $args);
            }
        } else {
            $result = $this->prepareResult('No Model Found with name ' . $this->module . '-' . $this->model);
        }

        return $result;
    }

    public function getRecordSelect($args)
    {
        $classname = $this->getClassName($this->module, $this->model);

        if ($classname) {
            if (method_exists($classname, 'getRecordSelect')) {
                $classname->module = $this->module;
                $classname->model = $this->model;
                $result = $classname->getRecordSelect($args);
            }
        } else {
            $result = $this->prepareResult('No Model Found with name ' . $this->module . '-' . $this->model);
        }

        return $result;
    }

    public function createRecord($args = [])
    {
        $classname = $this->getClassName($this->module, $this->model);

        if ($classname) {
            if (method_exists($classname, 'createRecord')) {
                $classname->module = $this->module;
                $classname->model = $this->model;
                $result = $classname->createRecord($args);
            }
        } else {
            $result = $this->prepareResult('No Model Found with name ' . $this->module . '-' . $this->model);
        }

        return $result;
    }

    public function updateRecord($args = [])
    {
        $classname = $this->getClassName($this->module, $this->model);

        if ($classname) {
            if (method_exists($classname, 'updateRecord')) {
                $classname->module = $this->module;
                $classname->model = $this->model;
                $result = $classname->updateRecord($args);
            }
        } else {
            $result = $this->prepareResult('No Model Found with name ' . $this->module . '-' . $this->model);
        }

        return $result;
    }

    public function deleteRecord($id)
    {
        $classname = $this->getClassName($this->module, $this->model);

        if ($classname) {
            if (method_exists($classname, 'deleteRecord')) {
                $classname->module = $this->module;
                $classname->model = $this->model;
                $result = $classname->deleteRecord($id);
            }
        } else {
            $result = $this->prepareResult('No Model Found with name ' . $this->module . '-' . $this->model);
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
    //Fetching Vue
    public function fetchVue($current_uri)
    {
        $fetchvue = new FetchVue();

        return $fetchvue->fetchVue($current_uri);
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

    public function prepareResult($message, $is_many = false)
    {
        $result = [
            'module' => $this->module,
            'model' => $this->model,
            'status' => 0,
            'error' => 1,
            'message' => $message,
        ];
        if ($is_many) {
            $result['records'] = [];
        } else {
            $result['record'] = [];

        }

        return $result;

    }

    //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
    //General Classes
    private function getClassName()
    {
        $classname = 'Modules\\' . ucfirst($this->module) . '\Entities\\' . ucfirst(Str::camel($this->model));

        return (class_exists($classname)) ? new $classname() : false;
    }
}
