<?php

namespace Modules\Base\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Modules\Base\Classes\Datasetter;
use Modules\Base\Classes\Migration;
use Modules\Base\Classes\Modularize;

class BaseController extends Controller
{
    protected $user;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
        //var_dump(get_class($this));
        //var_dump(get_class());
        //var_dump(__CLASS__);
    }

   
    // http://127.0.0.1:8000/api/account/journal/?s[name][str]=test&s[name][ope]==&s[keyword]=test
    public function getAllRecords(Request $request, $module, $model)
    {
        $modularize = new Modularize($module, $model);
        // logic to get all records goes here

        $args = $request->query();

        $result = $modularize->getAllRecords($args);

        return Response::json($result);
    }

    public function getRecord(Request $request, $module, $model, $id)
    {
        $modularize = new Modularize($module, $model);

        $args = $request->query();

        $result = $modularize->getRecord($id, $args);

        return Response::json($result);
    }

    public function getRecordSelect(Request $request, $module, $model)
    {

        $modularize = new Modularize($module, $model);
        // logic to get all records goes here

        $args = $request->query();

        $result = $modularize->getRecordSelect($args);

        return Response::json($result);
    }

    public function createRecord(Request $request, $module, $model)
    {
        $modularize = new Modularize($module, $model);

        $args = $request->all();

        $result = $modularize->createRecord($args);

        return Response::json($result);

    }

    public function updateRecord(Request $request, $module, $model, $id)
    {
        $modularize = new Modularize($module, $model);

        $args = $request->all();

        $result = $modularize->updateRecord($id, $args);

        return Response::json($result);
    }

    /**
     * Function for deleting a record.
     *
     * @param int $id
     *
     * @return array
     */
    public function deleteRecord($module, $model, $id)
    {
        $modularize = new Modularize($module, $model);

        $result = $modularize->deleteRecord($id);

        return Response::json($result);
    }

}
