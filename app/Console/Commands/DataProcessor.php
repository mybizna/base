<?php

namespace Modules\Base\Console\Commands;

use Illuminate\Console\Command;
use Modules\Base\Classes\Datasetter;
use Modules\Base\Classes\Fetch\Rights;

/**
 * Class DataProcessor
 *
 * @package Modules\Base\Console\Commands
 */
class DataProcessor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mybizna:dataprocessor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'System Data Processor.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        define('MYBIZNA_MIGRATION', true);

        $data_setter = new Datasetter();
        $fetch_rights = new Rights();

        $data_setter->show_logs = true;

        $data_setter->dataProcess();
        // $fetch_rights->fetchRights(100);

        return 0;
    }
}
