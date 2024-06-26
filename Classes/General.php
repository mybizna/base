<?php

namespace Modules\Base\Classes;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Modules\Base\Classes\Datasetter;
use Modules\Base\Classes\Migration;

/**
 * General class
 *
 * This class is used to get the view settings for the guest, user and back views
 *
 * @package Modules\Base\Classes

 */
class General
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
     * Constructor
     *
     * This is the constructor of the General class
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Get guest view setting
     *
     * This function is used to get the view settings for the guest
     *
     * @param string $template
     *
     * @return array
     */
    public function getGuestViewSetting($template = 'guest')
    {
        $result = [
            'url' => url('/'),
            'data_list' => [],
            'db_list' => [],
            'template' => $template,
            'has_user' => false,
            'has_uptodate' => false,
            'has_setting' => Schema::hasTable('core_setting'),
        ];

        $uniqid = md5(rand());

        if (Cache::has('mybizna_uniqid')) {
            $uniqid = Cache::get('mybizna_uniqid');
        } else {
            Cache::put('mybizna_uniqid', $uniqid);
            Cache::put($uniqid, ['viewside' => 'frontend']);
        }

        $result['mybizna_uniqid'] = $uniqid;

        return $result;
    }

    /**
     * Get user view setting
     *
     * This function is used to get the view settings for the user
     *
     * @param string $template
     *
     * @return array
     */
    public function getUserViewSetting($template = 'user')
    {
        $result = [
            'url' => url('/'),
            'data_list' => [],
            'db_list' => [],
            'template' => $template,
            'has_user' => false,
            'has_uptodate' => false,
            'has_setting' => Schema::hasTable('core_setting'),
        ];

        $uniqid = md5(rand());

        if (Cache::has('mybizna_uniqid')) {
            $uniqid = Cache::get('mybizna_uniqid');
        } else {
            Cache::put('mybizna_uniqid', $uniqid);
            Cache::put($uniqid, ['viewside' => 'frontend']);
        }

        $result['mybizna_uniqid'] = $uniqid;

        return $result;
    }

    /**
     * Get back view setting
     *
     * This function is used to get the view settings for the back
     *
     * @param string $template
     *
     * @return array
     */
    public function getBackViewSetting($template = 'manage')
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
            'template' => $template,
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

            Cache::put('migration_db_list', $db_list);
            Cache::put('migration_data_list', $data_list);

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

        return $result;
    }
}
