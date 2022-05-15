<?php

namespace Modules\AbRouter\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Facades\DB;

class CreateDatabase extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'abrouter:create-database {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create database.';

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
     * @return mixed
     */
    public function handle()
    {
        $dbName = $this->argument('name');
        $db = DB::connection()->select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = "."'".$dbName."'");
        if(empty($db)) {
            DB::connection()->select('CREATE DATABASE ' . $dbName);
            $this->info('The ' . $dbName . ' database has been created');
        } else $this->info('The ' . $dbName . ' database exists');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the database.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
