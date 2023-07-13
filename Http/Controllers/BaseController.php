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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function manage(Request $request)
    {

        $migration = new Migration();
        $datasetter = new Datasetter();

        define('MYBIZNA_MIGRATION', true);

        $has_uptodate = $migration->hasUpToDate();

       // print_r(url('/')); exit;

        $result = [
            'url' => url('/'),
            'data_list' => [],
            'db_list' => [],
            'has_user' => false,
            'has_uptodate' => $has_uptodate,
            'has_setting' => Schema::hasTable('core_setting'),
        ];

        if ($has_uptodate) {

            $db_list = [];
            $data_list = [];

            $userCount = User::count();

            if ($userCount && defined('MYBIZNA_BASE_URL')) {
                $result['has_user'] = true;
            }

            $dbmodels = $migration->migrateModels(true);
            foreach ($dbmodels as $item) {
                $db_list[] = $item['class'];
            }

            $datamodels = $datasetter->migrateModels();
            foreach ($datamodels as $item) {
                $data_list[] = $item['class'];
            }

            if (defined('MYBIZNA_BASE_URL')) {
                $url = MYBIZNA_BASE_URL;
            }

            //print_r($db_list); exit;

            $request->session()->put('migration_db_list', $db_list);
            $request->session()->put('migration_data_list', $data_list);

            $result['data_list'] = array_keys($data_list);
            $result['db_list'] = array_keys($db_list);

        }

        return view('base::manage', $result);
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

    public function deleteRecord($module, $model, $id)
    {
        $modularize = new Modularize($module, $model);

        $result = $modularize->deleteRecord($id);

        return Response::json($result);
    }

}
