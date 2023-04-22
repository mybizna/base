<?php

namespace Modules\Base\Classes;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class FetchRights
{

    public $paths = [];

    public function __construct()
    {
        $groups = (is_file(base_path('../readme.txt'))) ? ['Modules/*', '../../*/Modules/*'] : ['Modules/*'];
        foreach ($groups as $key => $group) {
            $this->paths = array_merge($this->paths, glob(base_path($group)));
        }

    }

    public function fetchRights()
    {
        foreach ($this->paths as $key => $path) {
            $file_names = ['right', 'rights'];

            foreach ($file_names as $key => $file_name) {
                $position_file = $path . DIRECTORY_SEPARATOR . $file_name . '.php';
                if (file_exists($position_file)) {
                    include_once $position_file;
                }
            }
        }

        $user = User::find(1);
        $user->assignRole('administrator');

        return ['status' => true];
    }

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

    protected function getRole($name)
    {
        $role = Role::where(['name' => $name])->first();

        if (!$role) {
            $role = Role::create(['name' => $name]);
        }

        return $role;
    }

    protected function getPermission($name)
    {
        $permission = Permission::where(['name' => $name])->first();

        if (!$permission) {
            $permission = Permission::create(['name' => $name]);
        }

        return $permission;
    }

}