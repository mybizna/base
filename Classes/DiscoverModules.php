<?php

namespace Modules\Base\Classes;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DiscoverModules
{

    public function discoverModules()
    {

        return; 

        
        $DS = DIRECTORY_SEPARATOR;

        $modules_path = realpath(base_path()) . $DS . 'Modules';

        if (is_dir($modules_path)) {
            $dir = new \DirectoryIterator($modules_path);

            foreach ($dir as $fileinfo) {
                if (!$fileinfo->isDot() && $fileinfo->isDir()) {
                    $module_name = $fileinfo->getFilename();

                    $asset_folder = realpath(base_path()) . $DS . 'public' . $DS . 'mybizna' . $DS . 'assets';
                  
                    $module_folder = $modules_path . $DS . $module_name . $DS . 'views';
                    $public_folder = $asset_folder . $DS . Str::lower($module_name);

                    if (!File::isDirectory($asset_folder)) {
                        File::makeDirectory($asset_folder);
                    }

                    if (!File::exists($public_folder)) {
                        messageBag('modularize_fold_missing_error', __('Folder Missing error.'));

                        //File::makeDirectory($public_folder);
                        if (File::exists($module_folder)) {
                            if (function_exists('symlink')) {
                                symlink($module_folder, $public_folder);
                            }else{
                                File::copyDirectory($module_folder, $public_folder);
                            }
                        }
                    }

                }
            }
        }

        return;
    }
}
