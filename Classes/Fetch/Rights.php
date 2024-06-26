<?php

namespace Modules\Base\Classes\Fetch;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Class Rights
 *
 * The class is used to fetch the rights
 *
 * @package Modules\Base\Classes\Fetch
 */
class Rights
{

    /**
     * Paths
     *
     * @var array
     */
    public $paths = [];

    /**
     * Show logs
     *
     * @var boolean
     */
    public $show_logs = false;

    /**
     * File logging
     *
     * @var boolean
     */
    public $file_logging = false;

    /**
     * Rights constructor.
     *
     * The constructor is used to fetch the paths
     */
    public function __construct()
    {
        $groups = (is_file(base_path('../readme.txt'))) ? ['Modules/*', '../../*/Modules/*'] : ['Modules/*'];
        foreach ($groups as $key => $group) {
            $this->paths = array_merge($this->paths, glob(base_path($group)));
        }
    }

    /**
     * Fetch Rights
     *
     * The function is used to fetch the rights
     *
     * @param int $limit
     *
     * @return array
     */
    public function fetchRights($limit = 2)
    {
        $started_processing = false;
        $counter = 0;
        $result = ['status' => true, 'completed' => true];
        $last_path = '';

        if (Cache::has("fetch_right_last_path")) {
            $last_path = Cache::get("fetch_right_last_path", '');
        }

        // Loop through the paths
        foreach ($this->paths as $key => $path) {
            if ($counter == $limit) {
                $result['completed'] = false;
                break;
            }

            if ($last_path == '' || $path == $last_path) {
                $started_processing = true;
            }

            if (!$started_processing) {
                continue;
            }

            $file_names = ['right', 'rights'];
            foreach ($file_names as $key => $file_name) {
                $position_file = $path . DIRECTORY_SEPARATOR . $file_name . '.php';

                if (file_exists($position_file)) {
                    include_once $position_file;
                }
            }

            $counter = $counter + 1;

            Cache::put("fetch_right_last_path", $path, 3600);
        }

        $user = User::where('id', 1)->first();
        if ($user) {
            $user->assignRole('administrator');
        }

        if ($result['completed'] == true) {
            Cache::put("fetch_right_last_path", '', 3600);
        }

        return $result;
    }

    /**
     * Add Right
     *
     * The function is used to add the right
     *
     * @param string $module
     * @param string $model
     * @param string $role_name
     * @param bool $view
     * @param bool $add
     * @param bool $edit
     * @param bool $delete
     *
     * @return void
     */
    public function add_right($module, $model, $role_name, $view = false, $add = false, $edit = false, $delete = false)
    {
        $give_permission = [];
        $revoke_permission = [];

        $role = $this->getRole($role_name);

        $view_permission_name = $module . "_" . $model . "_view";
        $add_permission_name = $module . "_" . $model . "_add";
        $edit_permission_name = $module . "_" . $model . "_edit";
        $delete_permission_name = $module . "_" . $model . "_delete";

        $this->getPermission($view_permission_name);
        $this->getPermission($add_permission_name);
        $this->getPermission($edit_permission_name);
        $this->getPermission($delete_permission_name);

        if ($view) {
            $give_permission[] = $view_permission_name;
        } else {
            $revoke_permission[] = $view_permission_name;
        }

        if ($add) {
            $give_permission[] = $add_permission_name;
        } else {
            $revoke_permission[] = $add_permission_name;

        }

        if ($edit) {
            $give_permission[] = $edit_permission_name;
        } else {
            $revoke_permission[] = $edit_permission_name;

        }

        if ($delete) {
            $give_permission[] = $delete_permission_name;
        } else {
            $revoke_permission[] = $delete_permission_name;

        }

        if (!empty($give_permission)) {
            $role->givePermissionTo($give_permission);
        }
        if (!empty($revoke_permission)) {
            $role->revokePermissionTo($revoke_permission);
        }
    }
    /**
     * Get Role
     *
     * The function is used to get the role
     *
     * @param string $name
     *
     * @return void
     */
    protected function getRole($name)
    {
        $role = Role::where(['name' => $name])->first();

        if (!$role) {
            $role = Role::create(['name' => $name]);
        }

        return $role;

    }
    /**
     * Get Permission
     *
     * The function is used to get the permission
     *
     * @param string $name
     *
     * @return void
     */
    protected function getPermission($name)
    {
        $permission = Permission::where(['name' => $name])->first();

        if (!$permission) {
            $permission = Permission::create(['name' => $name]);
        }

        return $permission;
    }

    /**
     * Log Output
     *
     * The function is used to log the output
     *
     * @param string $message
     * @param string $type
     *
     * @return void
     */
    private function logOutput($message, $type = 'info')
    {
        $output = new \Symfony\Component\Console\Output\ConsoleOutput();

        if ($this->show_logs) {
            if ($type == 'title') {
                if ($this->file_logging) {
                    Log::channel('datasetter')->info('');
                    Log::channel('datasetter')->info($message);
                    Log::channel('datasetter')->info('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
                    Log::channel('datasetter')->info('');
                } else {
                    $output->writeln("<info></info>");
                    $output->writeln("<info>$message</info>");
                    $output->writeln("<info>xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx</info>");
                    $output->writeln("<info></info>");
                }
            } else {
                if ($this->file_logging) {
                    Log::channel('datasetter')->info($message);
                } else {
                    $output->writeln("<info>$message</info>");
                }
            }
        }

    }

}
