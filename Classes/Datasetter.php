<?php

namespace Modules\Base\Classes;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Core\Entities\DataMigrated;

class Datasetter
{
    public $paths = [];
    public $show_logs = false;
    public $file_logging = false;

    public function __construct()
    {
        $groups = (is_file(base_path('../readme.txt'))) ? ['Modules/*', '../../*/Modules/*'] : ['Modules/*'];
        foreach ($groups as $key => $group) {
            $this->paths = array_merge($this->paths, glob(base_path($group)));
        }

    }

    //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
    //Data Modules
    public function dataProcess()
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
                                'object' => $object = app($model),
                                'order' => $object->ordering ?? 0,
                            ]);
                        }
                    }
                }
            }
        }

        foreach ($models->sortBy('order') as $model) {
            $this->logOutput('Model: ' . $model['data_folder'], 'title');

            if (!isset($model['object']->run_later) || !$model['object']->run_later) {
                $model['object']->data($this);
            }
        }

    }

    public function add_data($module, $model, $main_field, $data)
    {
        $data_to_migrate = array(
            'module' => $module,
            'table_name' => $model,
            "array_key" => $data[$main_field],
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

                if (!$saved_record->is_modified) {
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

    private function getClassName($module, $model)
    {
        $classname = 'Modules\\' . ucfirst($module) . '\Entities\\' . ucfirst(Str::camel($model));

        $this->logOutput($classname);

        return (class_exists($classname)) ? new $classname() : false;
    }
}
