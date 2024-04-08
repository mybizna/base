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
use Modules\Base\Classes\Autocomplete;
use Modules\Base\Classes\Datasetter;
use Modules\Base\Classes\General;
use Modules\Base\Classes\Migration;
use Modules\Base\Classes\Modularize;

class GeneralController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function guest(Request $request): \Illuminate\Contracts\Support\Renderable
    {

        $general = new General();

        $result = $general->getGuestViewSetting();

        return view('base::guest', $result);
    }

    /**
     * Show the application dashboard.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function user(Request $request): \Illuminate\Contracts\Support\Renderable
    {

        $general = new General();

        $result = $general->getUserViewSetting();

        return view('base::user', $result);
    }

    /**
     * Show the application dashboard.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function manage(Request $request): \Illuminate\Contracts\Support\Renderable
    {
        $general = new General();

        $result = $general->getBackViewSetting();

        return view('base::manage', $result);
    }

    /**
     * fetch the vue content
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     *
     */

    public function fetchVue(Request $request): \Illuminate\Http\Response
    {
        $current_uri = $request->segments();

        $modularize = new Modularize();

        // fetch the vue file
        [$contents, $status] = $modularize->fetchVue($current_uri);

        // create a response
        $response = Response::make($contents, $status);

        // set the content type
        $response->header('Content-Type', 'application/javascript');

        // set the code
        $response->setStatusCode($status);

        return $response;

    }

    /**
     * fetch the layout
     *
     * @param Request $request
     * @param string $module
     * @param string $model
     * @param string $action
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function fetchLayout(Request $request, $module, $model, $action): \Illuminate\Http\JsonResponse
    {
        $params = $request->all();

        $params['module'] = $module;
        $params['model'] = $model;
        $params['action'] = $action;

        $modularize = new Modularize();

        $result = $modularize->fetchLayout($params);

        return Response::json($result);

    }

    /**
     * fetch the routes
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function fetchRoutes(Request $request): \Illuminate\Http\JsonResponse
    {
        $modularize = new Modularize();

        $result = $modularize->fetchRoutes();

        return Response::json($result);
    }

    /**
     * fetch the rights
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function fetchRights(Request $request): \Illuminate\Http\JsonResponse

    {
        $modularize = new Modularize();

        $result = $modularize->fetchRights();

        return Response::json($result);
    }

    /**
     * fetch the positions
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function fetchPositions(Request $request): \Illuminate\Http\JsonResponse
    {
        $modularize = new Modularize();

        $result = $modularize->fetchPositions();

        return Response::json($result);
    }

    /**
     * fetch the menus
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function fetchMenus(Request $request): \Illuminate\Http\JsonResponse
    {
        $modularize = new Modularize();

        $u = $request->get('u');

        $setting = [];
        if (Cache::has('mybizna_uniqid')) {
            $setting = Cache::get($u);
        }

        $result = $modularize->fetchMenus();

        return Response::json($result);
    }

    /**
     * fetch the settings
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function fetchSettings(Request $request): \Illuminate\Http\JsonResponse
    {
        $modularize = new Modularize();

        $result = $modularize->fetchSettings();

        return Response::json($result);
    }

    /**
     * fetch the settings
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function checkUser(Request $request): \Illuminate\Http\JsonResponse
    {
        $result = [
            "module" => "base",
            "status" => 0,
            "error" => 1,
            "message" => "Checking User",
        ];

        $data = $request->all();

        $user = User::where($data['name'], $data['value'])->first();

        if ($user) {
            $result['status'] = 1;
            $result['error'] = 0;
            $result['message'] = "User Found";
        } else {
            $result['status'] = 0;
            $result['error'] = 1;
            $result['message'] = "User Not Found";
        }

        return Response::json($result);
    }

    /**
     * Function to register a user
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerUser(Request $request): \Illuminate\Http\JsonResponse
    {
        $result = [
            "module" => "base",
            "status" => 0,
            "error" => 1,
            "message" => "Registering User",
        ];

        $data = $request->all();

        // merge first_name and last_name to name
        $data['name'] = $data['first_name'] . ' ' . $data['last_name'];

        $user = User::create($data);

        if ($user) {
            $result['status'] = 1;
            $result['error'] = 0;
            $result['message'] = "User Registered";
        } else {
            $result['status'] = 0;
            $result['error'] = 1;
            $result['message'] = "User Not Registered";
        }

        return Response::json($result);
    }

    /**
     * Function to login a user
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function currentUser(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->user = Auth::user();

        $user = $request->user();

        return Response::json($user);
    }

    /**
     * Function to logout a user
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->user = Auth::user();

        $user = $request->user();

        return Response::json($user);
    }

    /**
     * Function to get dashboard data
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboardData(Request $request): \Illuminate\Http\JsonResponse
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

    /**
     * Function for autocomplete inputs
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function autocomplete(Request $request): \Illuminate\Http\JsonResponse
    {
        $search = $request->get('search');
        $table_name = $request->get('table_name');
        $display_fields = $request->get('display_fields');
        $search_fields = $request->get('search_fields');

        $autocomplete = new Autocomplete();

        $records = $autocomplete->dataResult($search, $table_name, $display_fields, $search_fields);

        return $records;
    }

    /**
     * Function to clear cache
     *
     * @param Request $request
     *
     * @return array
     */
    public function clearCache(Request $request): array
    {
        Artisan::call('cache:clear');
        return ['message' => 'Application cache has been cleared', 'status' => true];

    }

    /**
     * Function to clear route cache
     *
     * @param Request $request
     *
     * @return array
     */
    public function routeCache(Request $request): array
    {
        Artisan::call('route:cache');
        return ['message' => 'Routes cache has been cleared', 'status' => true];
    }

    /**
     * Function to clear config cache
     *
     * @param Request $request
     *
     * @return array
     */
    public function configCache(Request $request): array
    {
        Artisan::call('config:cache');
        return ['message' => 'Config cache has been cleared', 'status' => true];
    }

    /**
     * Function to clear view cache
     *
     * @param Request $request
     *
     * @return array
     */
    public function viewClear(Request $request): array
    {
        Artisan::call('view:clear');
        return ['message' => 'View cache has been cleared', 'status' => true];
    }

    /**
     * Function for automigrator migration
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function automigratorMigrate(Request $request): \Illuminate\Http\JsonResponse

    {

        define('MYBIZNA_MIGRATION', true);

        $postData = $request->all();

        $status = false;
        $message = 'Dont know what happened';

        if (isset($postData['class']) && Cache::has('migration_db_list')) {

            $class = $postData['class'];

            $db_list = Cache::get('migration_db_list');

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

    /**
     * Function for data processor
     *
     * @param Request $request
     *
     * @return array
     */
    public function dataProcessor(Request $request): array
    {

        define('MYBIZNA_MIGRATION', true);

        Cache::forget('mybizna_base_migrating');

        $postData = $request->all();

        $message = 'Dont know what happened';

        if (isset($postData['class']) && Cache::has('migration_data_list')) {

            $class = $postData['class'];

            $data_list = Cache::get('migration_data_list');

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

    /**
     * Function to create a user
     *
     * @param Request $request
     *
     * @return array
     */
    public function createUser(Request $request): array
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

    /**
     * Function to reset all cache
     *
     * @param Request $request
     *
     * @return array
     */
    public function resetAll(Request $request): array
    {
        Artisan::call('cache:clear');
        Artisan::call('route:cache');
        Artisan::call('config:cache');
        Artisan::call('view:clear');

        return 'Clear all reset.';

        return ['message' => 'Clear all reset.', 'status' => true];
    }

}
