<?php

namespace Modules\Base\Classes;

use Doctrine\DBAL\Schema\Comparator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Modules\Base\Classes\Fetch\Rights;

/**
 * Class Migration
 *
 * @package Modules\Base\Classes
 */
class Migration
{
    /**
     * Models
     *
     * @var array
     */
    public $models = [];
    /**
     * Paths
     *
     * @var array
     */
    public $show_logs = true;
    /**
     * File Logging
     *
     * @var bool
     */
    public $file_logging = false;

    /**
     * Function for checking if a key exists
     *
     * @param $table
     * @param $field
     * @param string $type
     *
     * @return bool
     */
    public static function checkKeyExist($table, $field, $type = 'foreign')
    {
        $keys = DB::select(DB::raw("SHOW KEYS from $table"));
        $key_name = $table . $field . $type;

        foreach ($keys as $item) {
            if ($item->Key_name == $key_name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add Foreign
     *
     * @param $table
     * @param $foreign_name
     * @param $field_name
     * @param string $type
     *
     * @return void
     */
    public static function addForeign($table, $foreign_name, $field_name, $type = 'foreign')
    {
        $table_name = $table->getTable();

        if (self::checkKeyExist($table_name, $field_name)) {
            $this->fields->foreign($field_name)->references('id')->on($foreign_name)->nullOnDelete();
        }
    }

    /**
     * Migrate Models
     *
     * @param $show_logs
     *
     * @return void
     */
    public function hasUpToDate()
    {
        $versions = $this->getVersions();

        if (empty($versions)) {
            return true;
        }

        if (Cache::has('mybizna_base_migrating')) {
            return true;
        }

        $groups = (is_file(base_path('../readme.txt'))) ? ['Modules/*', '../../*/Modules/*'] : ['Modules/*'];

        foreach ($groups as $key => $group) {
            $paths = glob(base_path($group));

            foreach ($paths as $key => $path) {
                if (is_dir($path)) {
                    if (!is_file($path . DIRECTORY_SEPARATOR . 'composer.json')) {
                        continue;
                    }

                    $path_arr = array_reverse(explode('/', $path));
                    $module_name = $path_arr[0];

                    $composer = $this->getComposer($path);

                    if (!array_key_exists($module_name, $versions) || $versions[$module_name] !== $composer['version']) {
                        return true;
                    }
                }

            }
        }

        return false;

    }

    /**
     * Migrate Models
     *
     * @param $show_logs
     *
     * @return void
     */
    public function migrateModels($show_logs = true)
    {
        $this->$show_logs = $show_logs;

        $modules = [];
        $versions = [];

        $groups = (is_file(base_path('../readme.txt'))) ? ['Modules/*', '../../*/Modules/*'] : ['Modules/*'];
        foreach ($groups as $key => $group) {
            $paths = glob(base_path($group));

            foreach ($paths as $key => $path) {
                if (is_dir($path)) {
                    if (!is_file($path . DIRECTORY_SEPARATOR . 'composer.json')) {
                        continue;
                    }

                    $path_arr = array_reverse(explode('/', $path));
                    $module_name = $path_arr[0];

                    $composer = $this->getComposer($path);

                    $modules[$module_name] = true;
                    $versions[$module_name] = $composer['version'];

                    if ($module_name == 'Base') {
                        continue;
                    }

                    $namespace = 'Modules\\' . $module_name . '\\Entities';
                    $db_folder = $path . DIRECTORY_SEPARATOR . 'Entities';

                    if (is_dir($db_folder)) {
                        $db_dir = new \DirectoryIterator($db_folder);

                        foreach ($db_dir as $fileinfo) {
                            if ($fileinfo->isFile()) {
                                $data_name = $fileinfo->getFilename();

                                if (strpos($data_name, '.php') !== false) {

                                    $model = $namespace . str_replace(
                                        ['/', '.php'],
                                        ['\\', ''],
                                        '\\' . $data_name
                                    );

                                    if (is_subclass_of($model, Model::class) && method_exists($model, 'migration')) {

                                        $object = app($model);
                                        $table_name = $object->getTable();

                                        $this->logOutput("$table_name");

                                        $this->models[$table_name] = [
                                            //'object' => $object,
                                            'table' => $table_name,
                                            'class' => $model,
                                            'dependencies' => $object->migrationDependancy ?? [],
                                            'order' => $object->migrationOrder ?? 0,
                                            'processed' => false,
                                        ];

                                    }

                                }

                            }
                        }
                    }
                }
            }
        }

        $this->saveVersions($modules, $versions);

        $this->updateOrder($this->models);

        return collect($this->models)->sortBy('order');

    }

    /**
     * Migrate Models
     *
     * @param $models
     *
     * @return void
     */
    public function migrateModel(Model $model)
    {
        $rights = new Rights();

        $class_name = get_class($model);

        $this->logOutput($class_name);

        $modelTable = $model->getTable();
        $tempTable = 'table_' . $modelTable;

        //new Blueprint($this->table)

        Schema::dropIfExists($tempTable);

        Schema::create($tempTable, function (Blueprint $table) use ($model) {
            $model->migration($table);

            $table->boolean('is_modified')->default(false);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('delete_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

        });

        if (Schema::hasTable($modelTable)) {

            $manager = $model->getConnection()->getDoctrineSchemaManager();
            $manager->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

            $diff = (new Comparator)->diffTable($manager->listTableDetails($modelTable), $manager->listTableDetails($tempTable));

            if ($diff) {
                $manager->alterTable($diff);

                $this->logOutput(' -- Table ' . $modelTable . ' updated.');
            } else {
                $this->logOutput(' -- Table ' . $modelTable . ' is current.');
            }

            Schema::drop($tempTable);
        } else {
            Schema::rename($tempTable, $modelTable);

            $this->logOutput(' -- Table ' . $modelTable . ' created.');
        }

        if (method_exists($model, 'post_migration')) {
            try {
                $this->logOutput(' -- Post Migration Successful.');

                Schema::table($modelTable, function (Blueprint $table) use ($model) {
                    $model->post_migration($table);
                });
            } catch (\Throwable $th) {
                $this->logOutput(' -- Post Migration Failed.');
                throw $th;
            }
        }

        // Remove Modules/ and /Entities from the class name
        $class_name = str_replace(['Modules\\', 'Entities\\'], '', $class_name);
        $class_name_arr = explode('\\', $class_name);

        // Get the module name and change camel case to snake case using Str
        $module_name = Str::snake($class_name_arr[0]);

        // Get the model name and change camel case to snake case using Str
        $model_name = Str::snake($class_name_arr[1]);

        $right_arr = $model->rights() ?? [];

        // loop through the array and add the rights
        foreach ($right_arr as $role => $right) {
            $rights->add_right($module_name, $model_name, $role, $right['view'] ?? false, $right['add'] ?? false, $right['edit'] ?? false, $right['delete'] ?? false);
        }

    }

    /**
     * Update Order
     *
     * @return void
     */
    private function updateOrder()
    {
        foreach ($this->models as $table_name => $model) {
            $this->processDependencies($table_name);
        }
    }

    /**
     * Process Dependencies
     *
     * @param $table_name
     * @param int $call_count
     *
     * @return void
     */
    private function processDependencies($table_name, $call_count = 0)
    {
        $orders = [];

        if ($call_count > 20) {
            $this->logOutput('Error processing dependencies. To many calls ' . $table_name . '.');
            return;
        }

        try {
            if (!empty($this->models[$table_name]['dependencies']) && !$this->models[$table_name]['processed']) {

                foreach ($this->models[$table_name]['dependencies'] as $dependency) {
                    $this->processDependencies($dependency, $call_count + 1);
                    array_push($orders, $this->models[$dependency]['order']);
                }

                if (!empty($orders)) {

                    sort($orders);

                    $order = (int) array_pop($orders) + 1;

                    if ($order) {
                        $this->models[$table_name]['order'] = $order;
                    }
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
            $this->logOutput('Error with table ' . $table_name);
        }

        $this->models[$table_name]['processed'] = true;
    }

    /**
     * Save Versions
     *
     * @param $modules
     * @param $versions
     *
     * @return void
     */
    private function saveVersions($modules, $versions)
    {
        ksort($modules);
        ksort($versions);

        Cache::forget('mybizna_base_modules');
        Cache::forget('mybizna_base_versions');
        Cache::forget('mybizna_base_migrating');

        Cache::forever('mybizna_base_modules', $modules);
        Cache::forever('mybizna_base_versions', $versions);
        Cache::forever('mybizna_base_migrating', true);
    }

    /**
     * Get Versions
     *
     * @return array
     */
    private function getVersions()
    {
        if (Cache::has('mybizna_base_versions')) {
            return Cache::get('mybizna_base_versions');
        }

        return [];
    }

    /**
     * Get Composer
     *
     * @param $path
     *
     * @return array
     */

    private function getComposer($path)
    {
        $path = $path . DIRECTORY_SEPARATOR . 'composer.json';

        $json = file_get_contents($path);

        return json_decode($json, true);
    }
    /**
     * Log Output
     *
     * @param $message
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
                    Log::channel('migration')->info('');
                    Log::channel('migration')->info($message);
                    Log::channel('migration')->info('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
                    Log::channel('migration')->info('');
                } else {
                    $output->writeln("<info></info>");
                    $output->writeln("<info>$message</info>");
                    $output->writeln("<info>xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx</info>");
                    $output->writeln("<info></info>");
                }
            } else {
                if ($this->file_logging) {
                    Log::channel('migration')->info($message);
                } else {
                    $output->writeln("<info>$message</info>");
                }
            }
        }
    }
}
