<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ExportToHostingMysql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:export-hosting-sql {--output= : Custom path for the output .sql file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export local SQLite database records to MySQL/MariaDB compatible SQL for production hosting';

    public function handle(): int
    {
        $this->info('Generating MySQL/MariaDB migration script for hosting database...');

        $tables = [
            'users',
            'categories',
            'products',
            'product_images',
            'furniture_sets',
            'furniture_set_product',
            'settings',
            'inquiries',
        ];

        $output = "-- ==========================================================\n";
        $output .= "-- Nusarattan: SQLite to MySQL / MariaDB Hosting Migration\n";
        $output .= '-- Generated: '.now()->toIso8601String()."\n";
        $output .= "-- Compatible with: cPanel phpMyAdmin, Adminer, MySQL CLI\n";
        $output .= "-- ==========================================================\n\n";
        $output .= "SET NAMES utf8mb4;\n";
        $output .= "SET FOREIGN_KEY_CHECKS = 0;\n";
        $output .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n\n";

        $totalRows = 0;

        foreach ($tables as $table) {
            if (! DB::getSchemaBuilder()->hasTable($table)) {
                continue;
            }

            $rows = DB::table($table)->get();
            if ($rows->isEmpty()) {
                continue;
            }

            $output .= "-- --------------------------------------------------------\n";
            $output .= "-- Data for table `{$table}` (".count($rows)." rows)\n";
            $output .= "-- --------------------------------------------------------\n";

            foreach ($rows as $row) {
                $totalRows++;
                $rowArray = (array) $row;
                $columns = array_keys($rowArray);
                $values = [];

                foreach ($rowArray as $col => $val) {
                    if ($val === null) {
                        $values[] = 'NULL';
                    } elseif (is_bool($val)) {
                        $values[] = $val ? '1' : '0';
                    } elseif (is_numeric($val) && ! is_string($val)) {
                        $values[] = $val;
                    } else {
                        // In MySQL booleans are TINYINT(1) 1/0
                        if (in_array($col, ['is_active', 'is_featured', 'is_primary'])) {
                            $values[] = ($val == 1 || $val === '1' || $val === true) ? '1' : '0';
                        } else {
                            $escaped = str_replace(
                                ['\\', "\0", "\n", "\r", "'", '"', "\x1a"],
                                ['\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'],
                                (string) $val
                            );
                            $values[] = "'{$escaped}'";
                        }
                    }
                }

                $colList = implode(', ', array_map(fn ($c) => "`{$c}`", $columns));
                $valList = implode(', ', $values);

                $output .= "INSERT INTO `{$table}` ({$colList}) VALUES ({$valList}) ON DUPLICATE KEY UPDATE `id` = `id`;\n";
            }

            $output .= "\n";
        }

        $output .= "SET FOREIGN_KEY_CHECKS = 1;\n";

        $outputPath = $this->option('output') ?: database_path('hosting_mysql_migration.sql');
        File::put($outputPath, $output);

        $this->info("✓ Successfully exported {$totalRows} records to: {$outputPath}");
        $this->newLine();
        $this->comment('How to import into your hosting database:');
        $this->line('1. In your hosting cPanel, create a MySQL database and run Laravel migrations:');
        $this->line('   php artisan migrate --force (with DB_CONNECTION=mysql in your hosting .env)');
        $this->line('2. Then open phpMyAdmin in cPanel -> Select your database -> Click "Import"');
        $this->line('3. Choose file: database/hosting_mysql_migration.sql -> Click "Import"');
        $this->line('   Or run via SSH/terminal:');
        $this->line('   mysql -u username -p database_name < database/hosting_mysql_migration.sql');

        return self::SUCCESS;
    }
}
