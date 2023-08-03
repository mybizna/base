<?php

namespace Modules\Base\Classes;

use Auth;
use Illuminate\Support\Str;
use Modules\Base\Classes\Fetch\Menus;
use Modules\Base\Classes\Fetch\Positions;
use Modules\Base\Classes\Fetch\Rights;
use Modules\Base\Classes\Fetch\Routes;
use Modules\Base\Classes\Fetch\Settings;
use Modules\Base\Classes\Fetch\Vue;
use Modules\Base\Classes\Fetch\Layout;

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

    public function checkUserCan($user)
    {
        $user = Auth::user();

        $roles = $user->getRoleNames();

        if (empty($roles)) {
            $user->assignRole('registered');
        }

        return $user->can($this->module . "_" . $this->model . "_view");

    }

    public function getAllRecords($args)
    {
        $can = $this->checkUserCan($this->module . "_" . $this->model . "_view");

        if (!$can) {
            return $this->prepareResult('User does not have right to view ' . $this->module . '-' . $this->model, true);
        }

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
        $can = $this->checkUserCan($this->module . "_" . $this->model . "_view");

        if (!$can) {
            return $this->prepareResult('User does not have right to view ' . $this->module . '-' . $this->model, true);
        }

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
        $can = $this->checkUserCan($this->module . "_" . $this->model . "_view");

        if (!$can) {
            return $this->prepareResult('User does not have right to view ' . $this->module . '-' . $this->model, true);
        }

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
        $can = $this->checkUserCan($this->module . "_" . $this->model . "_add");

        if (!$can) {
            return $this->prepareResult('User does not have right to add ' . $this->module . '-' . $this->model, true);
        }

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

    public function updateRecord($id, $args = [])
    {
        $can = $this->checkUserCan($this->module . "_" . $this->model . "_edit");

        if (!$can) {
            return $this->prepareResult('User does not have right to edit ' . $this->module . '-' . $this->model, true);
        }

        $classname = $this->getClassName($this->module, $this->model);

        if ($classname) {
            if (method_exists($classname, 'updateRecord')) {
                $classname->module = $this->module;
                $classname->model = $this->model;
                $result = $classname->updateRecord($id, $args);
            }
        } else {
            $result = $this->prepareResult('No Model Found with name ' . $this->module . '-' . $this->model);
        }

        return $result;
    }

    /**
     * Function for deleting a record.
     *
     * @param int $id
     *
     * @return array
     */
    public function deleteRecord($id)
    {
        $can = $this->checkUserCan($this->module . "_" . $this->model . "_delete");

        if (!$can) {
            return $this->prepareResult('User does not have right to delete ' . $this->module . '-' . $this->model, true);
        }

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
        $fetchroutes = new Routes();

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
        $fetchvue = new Vue();

        return $fetchvue->fetchVue($current_uri);
    }


    //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
    //Fetch Layout
    public function fetchLayout($params)
    {
        $layout = new Layout();

        return $layout->fetchLayout($params);
    }

    //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
    //Fetching Rights
    public function fetchRights()
    {
        $fetchrights = new Rights();

        return $fetchrights->fetchRights();
    }

    //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
    //Fetching Menu
    public function fetchPositions()
    {
        $fetchpositions = new Positions();

        return $fetchpositions->fetchPositions();
    }

    //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
    //Fetching Menu
    public function fetchMenus()
    {
        $fetchmenus = new Menus();

        return $fetchmenus->fetchMenus();
    }

    //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
    //Fetching Setting
    public function fetchSettings()
    {
        $fetchsettings = new Settings();

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
