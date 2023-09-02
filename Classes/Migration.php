<?php

namespace Modules\Base\Classes;

use Doctrine\DBAL\Schema\Comparator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class Migration
{

    public $models = [];
    public $show_logs = true;
    public $file_logging = false;

    //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
    //Data Modules
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

    public static function addForeign($table, $foreign_name, $field_name, $type = 'foreign')
    {
        $table_name = $table->getTable();

        if (self::checkKeyExist($table_name, $field_name)) {
            $this->fields->foreign($field_name)->references('id')->on($foreign_name)->nullOnDelete();
        }
    }

    public function hasUpToDate()
    {
        $versions = $this->getVersions();

        if (empty($versions)) {
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

                    if ($versions[$module_name] !== $composer['version']) {

                        return true;
                    }
                }

            }
        }

        return false;

    }

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

    public function migrateModel(Model $model)
    {
        $this->logOutput(get_class($model));

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
    }

    private function updateOrder()
    {
        foreach ($this->models as $table_name => $model) {
            $this->processDependencies($table_name);
        }
    }

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

    private function saveVersions($modules, $versions)
    {
        ksort($modules);
        ksort($versions);

        Cache::forget('mybizna_base_modules');
        Cache::forget('mybizna_base_versions');

        Cache::forever('mybizna_base_modules', $modules);
        Cache::forever('mybizna_base_versions', $versions);
    }

    private function getVersions()
    {
        if (Cache::has('mybizna_base_versions')) {
            return Cache::get('mybizna_base_versions');
        }

        return [];
    }

    private function getComposer($path)
    {
        $path = $path . DIRECTORY_SEPARATOR . 'composer.json';

        $json = file_get_contents($path);

        return json_decode($json, true);
    }

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
