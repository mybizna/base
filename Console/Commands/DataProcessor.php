<?php

namespace Modules\Base\Console\Commands;

use Illuminate\Console\Command;
use Modules\Base\Classes\Datasetter;

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
        $data_setter = new Datasetter();

        $data_setter->show_logs = true;

        $data_setter->dataProcess();

        return 0;
    }
}
