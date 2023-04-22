<?php

namespace Modules\Base\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Modules\Base\Classes\Autocomplete;
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
    public function manage()
    {
        return view('base::manage');
    }

    public function fetchVue(Request $request)
    {
        $current_uri = $request->segments();

        $modularize = new Modularize();

        [$contents, $status] = $modularize->fetchVue($current_uri);

        $response = Response::make($contents, $status);

        $response->header('Content-Type', 'application/javascript');

        return $response;

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

        exit;
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

    public function discoverModules(Request $request)
    {
        $modularize = new Modularize();

        $result = $modularize->discoverModules();

        return Response::json($result);
    }

    public function fetchRoutes(Request $request)
    {
        $modularize = new Modularize();

        $result = $modularize->fetchRoutes();

        return Response::json($result);
    }

    public function fetchRights(Request $request)
    {
        $modularize = new Modularize();

        $result = $modularize->fetchRights();

        return Response::json($result);
    }
    public function fetchPositions(Request $request)
    {
        $modularize = new Modularize();

        $result = $modularize->fetchPositions();

        return Response::json($result);
    }

    public function fetchMenus(Request $request)
    {
        $modularize = new Modularize();

        $result = $modularize->fetchMenus();

        return Response::json($result);
    }

    public function fetchSettings(Request $request)
    {
        $modularize = new Modularize();

        $result = $modularize->fetchSettings();

        return Response::json($result);
    }

    public function currentUser(Request $request)
    {

        $this->user = Auth::user();

        $user = $request->user();

        return Response::json($user);
    }

    public function profile(Request $request)
    {

        $this->user = Auth::user();

        $user = $request->user();

        return Response::json($user);
    }

    public function dashboardData(Request $request)
    {

        $result = [
            [
                'is_amount' => false,
                'title' => "Purchase",
                'icon' => "fas fa-chart-line",
                'color' => "primary",
                'total' => DB::table('account_transaction')->count(),
            ],
            [
                'is_amount' => false,
                'title' => "Partner",
                'icon' => "fas fa-users",
                'color' => "success",
                'total' => DB::table('partner')->count(),
            ],
            [
                'is_amount' => false,
                'title' => "Product",
                'icon' => "fas fa-store",
                'color' => "warning",
                'total' => DB::table('product')->count(),
            ],
            [
                'is_amount' => true,
                'title' => "Sales",
                'icon' => "fas fa-sack-dollar",
                'color' => "info",
                'total' => DB::table('sale')->count(),
            ],
        ];

        return Response::json($result);
    }

    public function autocomplete(Request $request)
    {
        $search = $request->get('search');
        $table_name = $request->get('table_name');
        $display_fields = $request->get('display_fields');
        $search_fields = $request->get('search_fields');

        $autocomplete = new Autocomplete();

        $records = $autocomplete->dataResult($search, $table_name, $display_fields, $search_fields);

        return $records;
    }
}
