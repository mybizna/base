<?php

namespace Modules\Base\Classes;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Core\Entities\DataMigrated;

/**
 * Class Datasetter
 *
 * The class is used to set the data for the modules
 *
 * @package Modules\Base\Classes
 */
class Datasetter
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
     * Datasetter constructor.
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
     * Data Process
     *
     * The function is used to process the data
     * 
     * @return void
     */
    public function dataProcess()
    {
        $models = $this->migrateModels();

        foreach ($models->sortBy('order') as $model) {
            $this->migrateModel($model['object']);
        }
    }

    /**
     * Migrate Models
     *
     * The function is used to migrate the models
     * 
     * @return Collection
     */
    public function migrateModels()
    {
        $models = collect();

        foreach ($this->paths as $key => $path) {

            $path_arr = array_reverse(explode('/', $path));
            $module_name = $path_arr[0];

            $namespace = 'Modules\\' . $module_name . '\\Entities\\Data';
            $data_folder = $path . DIRECTORY_SEPARATOR . 'Entities' . DIRECTORY_SEPARATOR . 'Data';

            if (is_dir($data_folder)) {
                $data_dir = new \DirectoryIterator($data_folder);

                foreach ($data_dir as $fileinfo) {
                    if ($fileinfo->isFile()) {
                        $data_name = $fileinfo->getFilename();

                        $model = $namespace . str_replace(
                            ['/', '.php'],
                            ['\\', ''],
                            '\\' . $data_name
                        );

                        if (method_exists($model, 'data')) {
                            $models->push([
                                'data_folder' => $data_folder,
                                'class' => $model,
                                'object' => $object = app($model),
                                'order' => $object->ordering ?? 0,
                            ]);
                        }
                    }
                }
            }
        }

        return $models->sortBy('order');

    }

    /**
     * Migrate Model
     *
     * The function is used to migrate the model
     * 
     * @param $model
     * 
     * @return void
     */
    public function migrateModel($model)
    {
        $this->logOutput('Model: ' . get_class($model), 'title');

        if (!isset($model->run_later) || !$model->run_later) {
            $model->data($this);
        }
    }

    /**
     * Add Data
     *
     * The function is used to add the data
     * 
     * @param $module
     * @param $model
     * @param $main_field
     * @param $data
     * 
     * @return void
     */
    public function add_data($module, $model, $main_field, $data)
    {
        $main_fields = (is_array($main_field)) ? $main_field : [$main_field];

        $slug = '';
        foreach ($main_fields as $key => $field) {
            $slug .= $data[$field];
        }

        $data_to_migrate = array(
            'module' => $module,
            'table_name' => $model,
            "array_key" => $slug,
        );

        $class_name = $this->getClassName($module, $model);

        array_multisort($data);
        $json_data = json_encode($data);
        $hash = md5($json_data);

        $this->logOutput($json_data);

        $data_migrated = DataMigrated::where($data_to_migrate)
            ->whereNotNull('item_id')->first();

        if ($data_migrated && $data_migrated->item_id) {
            if ($hash != $data_migrated->hash) {
                $saved_record = $class_name::find($data_migrated->item_id);

                if ($saved_record && !$saved_record->is_modified) {
                    $saved_record->fill($data);
                    $saved_record->save();
                }

                $data_migrated->hash = $hash;
                $data_migrated->counter = $data_migrated->counter + 1;
                $data_migrated->save();
            }
        } else {
            $data = $class_name::create($data);

            $data_to_migrate['item_id'] = $data->id;
            $data_to_migrate['hash'] = $hash;

            DataMigrated::create($data_to_migrate);
        }
    }

    /**
     * Add Data
     *
     * The function is used to add the data
     * 
     * @param $module
     * @param $model
     * @param $main_field
     * @param $data
     * 
     * @return void
     */
    public function initiateUser($user = [])
    {
        //Create Admin User
        if (!empty($user)) {
            $user_cls = new User();
            $user_cls->password = Hash::make($user['password']);
            $user_cls->email = $user['email'];
            $user_cls->name = $user['name'];
            $user_cls->save();
        } else {
            $userCount = User::count();

            if (!$userCount) {

                $user_cls = new User();

                if (defined('MYBIZNA_USER_LIST')) {
                    $wp_user_list = MYBIZNA_USER_LIST;
                    foreach ($wp_user_list as $key => $wp_user) {
                        $user_cls->password = Hash::make(uniqid());
                        $user_cls->email = $wp_user->user_email;
                        $user_cls->name = $wp_user->user_nicename;
                        $user_cls->save();
                    }

                } else {

                    $user_cls->password = Hash::make('admin');
                    $user_cls->email = 'admin@admin.com';
                    $user_cls->name = 'Admin User';
                    $user_cls->save();
                }
            }
        }

    }

    /**
     * Log Output
     *
     * The function is used to log the output
     * 
     * @param $message
     * @param $type
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

    /**
     * Get Class Name
     *
     * The function is used to get the class name
     * 
     * @param $module
     * @param $model
     * 
     * @return void
     */
    private function getClassName($module, $model)
    {
        $classname = 'Modules\\' . ucfirst($module) . '\Entities\\' . ucfirst(Str::camel($model));

        $this->logOutput($classname);

        return (class_exists($classname)) ? new $classname() : false;
    }
}
