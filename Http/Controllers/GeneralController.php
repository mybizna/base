<?php

namespace Modules\Base\Http\Controllers;

use App\Models\User;
use Artisan;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Modules\Base\Classes\Autocomplete;
use Modules\Base\Classes\Datasetter;
use Modules\Base\Classes\Migration;
use Modules\Base\Classes\Modularize;

class GeneralController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function front(Request $request)
    {

        $result = [
            'url' => url('/'),
            'data_list' => [],
            'db_list' => [],
            'has_user' => false,
            'has_uptodate' => false,
            'has_setting' => Schema::hasTable('core_setting'),
        ];

        //print_r($result); exit;

        $uniqid = md5(rand());

        if (Cache::has('mybizna_uniqid')) {
            $uniqid = Cache::get('mybizna_uniqid');
        } else {
            Cache::put('mybizna_uniqid', $uniqid);
            Cache::put($uniqid, ['viewside' => 'frontend']);
        }

        $result['mybizna_uniqid'] = $uniqid;

        return view('base::front', $result);
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

            if ($userCount || defined('MYBIZNA_BASE_URL')) {
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

            $request->session()->put('migration_db_list', $db_list);
            $request->session()->put('migration_data_list', $data_list);

            $result['data_list'] = array_keys($data_list);
            $result['db_list'] = array_keys($db_list);

        }

        $uniqid = md5(rand());

        if (Cache::has('mybizna_uniqid')) {
            $uniqid = Cache::get('mybizna_uniqid');
        } else {
            Cache::put('mybizna_uniqid', $uniqid);
            Cache::put($uniqid, ['viewside' => 'frontend']);
        }

        $result['mybizna_uniqid'] = $uniqid;

        return view('base::manage', $result);
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

    public function fetchLayout(Request $request, $module, $model, $action)
    {
        $params = $request->all();

        $params['module'] = $module;
        $params['model'] = $model;
        $params['action'] = $action;

        $modularize = new Modularize();

        $result = $modularize->fetchLayout($params);

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

        $u = $request->get('u');

        $setting = [];
        if (Cache::has('mybizna_uniqid')) {
            $setting = Cache::get($u);
        }
        
        $viewside = (isset($setting['viewside'])) ? $setting['viewside'] : 'backend';
        
        $result = $modularize->fetchMenus($viewside);

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

    public function clearCache(Request $request)
    {
        Artisan::call('cache:clear');
        return ['message' => 'Application cache has been cleared', 'status' => true];

    }

    public function routeCache(Request $request)
    {
        Artisan::call('route:cache');
        return ['message' => 'Routes cache has been cleared', 'status' => true];
    }

    public function configCache(Request $request)
    {
        Artisan::call('config:cache');
        return ['message' => 'Config cache has been cleared', 'status' => true];
    }

    public function viewClear(Request $request)
    {
        Artisan::call('view:clear');
        return ['message' => 'View cache has been cleared', 'status' => true];
    }

    public function automigratorMigrate(Request $request)
    {

        define('MYBIZNA_MIGRATION', true);

        $postData = $request->all();

        $status = false;
        $message = 'Dont know what happened';

        if (isset($postData['class']) && $request->session()->has('migration_db_list')) {
            $class = $postData['class'];
            $db_list = $request->session()->get('migration_db_list');

            if (in_array($class, array_keys($db_list))) {
                $classname = $db_list[$class];

                $Migration = new Migration();
                $Migration->migrateModel(app($classname));
                $message = "Entity Class $classname Migrated Successfully";
                $status = true;

            } else {
                $message = "Class $class not found";
            }

        } else {
            $message = 'No class sent in the request';
        }

        return response()->json(['message' => $message, 'status' => $status]);

    }

    public function dataProcessor(Request $request)
    {

        define('MYBIZNA_MIGRATION', true);

        $postData = $request->all();

        $message = 'Dont know what happened';

        if (isset($postData['class']) && $request->session()->has('migration_data_list')) {
            $class = $postData['class'];
            $data_list = $request->session()->get('migration_data_list');

            if (in_array($class, array_keys($data_list))) {
                $classname = $data_list[$class];

                $datasetter = new Datasetter();
                $datasetter->migrateModel(app($classname));

                $message = "Data Class $classname Processed Successfully";

            } else {
                $message = "Class $class not found";
            }

        } else {
            $message = 'No class sent in the request';
        }

        return ['message' => $message, 'status' => true];
    }

    public function createUser(Request $request)
    {
        $status = false;
        $message = 'Dont know what happened';

        $datasetter = new Datasetter();

        $post = $request->post();

        if (defined('MYBIZNA_PLUGINS_URL')) {
            $datasetter->initiateUser();

            $message = "Wordpress User Migrated Successfully";
            $status = true;
        } else {
            if (isset($post['username']) && isset($post['password']) && isset($post['email'])) {
                $datasetter->initiateUser($post);
                return ['message' => 'User created', 'status' => true];
                $message = 'User created';
                $status = true;
            } else {
                $message = 'User not created: Username, Password and Email are required';
                $status = false;
            }

        }

        return ['message' => $message, 'status' => $status];
    }

    public function resetAll(Request $request)
    {
        Artisan::call('cache:clear');
        Artisan::call('route:cache');
        Artisan::call('config:cache');
        Artisan::call('view:clear');

        return 'Clear all reset.';

        return ['message' => 'Clear all reset.', 'status' => true];
    }

}
