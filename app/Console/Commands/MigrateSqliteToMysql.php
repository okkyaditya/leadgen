<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateSqliteToMysql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:migrate-sqlite-to-mysql';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate data from the local SQLite database to the default MySQL database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database migration from SQLite to MySQL...');

        // 1. Set the SQLite connection dynamic path to ensure it always points to the correct database file
        $sqlitePath = database_path('database.sqlite');
        if (!file_exists($sqlitePath)) {
            $this->error("SQLite database file not found at: {$sqlitePath}");
            return Command::FAILURE;
        }

        config(['database.connections.sqlite_temp' => [
            'driver' => 'sqlite',
            'database' => $sqlitePath,
            'prefix' => '',
            'foreign_key_constraints' => false,
        ]]);

        $sqliteConn = DB::connection('sqlite_temp');
        $mysqlConn = DB::connection(); // default is now mysql

        // 2. Confirm if the user wants to proceed
        if (!$this->confirm('This will empty the target MySQL database tables before copying data. Do you want to continue?', true)) {
            $this->info('Migration cancelled.');
            return Command::SUCCESS;
        }

        // 3. Define the tables to copy, in order of dependencies
        $tables = [
            'cabangs',
            'users',
            'roles',
            'permissions',
            'model_has_roles',
            'model_has_permissions',
            'role_has_permissions',
            'leads',
            'audit_logs',
            'upline_change_requests',
        ];

        // 4. Disable foreign key checks in MySQL
        $this->info('Disabling foreign key constraints on MySQL...');
        $mysqlConn->statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            foreach ($tables as $table) {
                if (!Schema::connection('sqlite_temp')->hasTable($table)) {
                    $this->warn("Table '{$table}' does not exist in SQLite database. Skipping.");
                    continue;
                }

                if (!Schema::hasTable($table)) {
                    $this->error("Table '{$table}' does not exist in MySQL database. Please run 'php artisan migrate' first.");
                    $mysqlConn->statement('SET FOREIGN_KEY_CHECKS=1;');
                    return Command::FAILURE;
                }

                $this->info("Migrating table: {$table}...");

                // Truncate target table
                $mysqlConn->table($table)->truncate();

                // Fetch data from SQLite
                $rows = $sqliteConn->table($table)->get();

                if ($rows->isEmpty()) {
                    $this->line("Table '{$table}' is empty. Skipping copy.");
                    continue;
                }

                $bar = $this->output->createProgressBar($rows->count());
                $bar->start();

                // Insert into MySQL in chunks to prevent memory limit issues
                $data = [];
                foreach ($rows as $row) {
                    // Convert stdClass object to associative array
                    $data[] = (array) $row;
                    
                    if (count($data) >= 100) {
                        $mysqlConn->table($table)->insert($data);
                        $bar->advance(count($data));
                        $data = [];
                    }
                }

                if (!empty($data)) {
                    $mysqlConn->table($table)->insert($data);
                    $bar->advance(count($data));
                }

                $bar->finish();
                $this->line('');
            }

            $this->info('Data successfully migrated from SQLite to MySQL!');
        } catch (\Exception $e) {
            $this->error('An error occurred during migration: ' . $e->getMessage());
            $mysqlConn->statement('SET FOREIGN_KEY_CHECKS=1;');
            return Command::FAILURE;
        }

        // 5. Re-enable foreign key checks in MySQL
        $mysqlConn->statement('SET FOREIGN_KEY_CHECKS=1;');
        return Command::SUCCESS;
    }
}
