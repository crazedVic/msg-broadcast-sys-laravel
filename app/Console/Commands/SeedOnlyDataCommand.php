<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedOnlyDataCommand extends Command
{
    protected $signature = 'migrate:freshdata';
    protected $description = 'Run fresh migrations and seed';

    public function handle()
    {
        // Run fresh migrations
        $totalMigrations = DB::table('migrations')->count();
        $this->call('migrate:rollback', ['--step' => $totalMigrations - 5 ]);

        $this->call('migrate');

        // Run seeder with type
        app()->instance('data_only', true);
        
        $this->call('db:seed', ['--class' => 'DatabaseSeeder']);
        
        //$this->call('db:seed', ['--data' => 'only']);
        //$this->call('db:seed', ['--class' => 'DatabaseSeeder', '--data' => true]);


        $this->info('Data refresh and seeding completed.');
    }
}
