<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

abstract class BaseSeeder extends Seeder
{
    /**
     * Safely truncate a table by disabling foreign key checks.
     *
     * @param  string  $modelClass
     * @return void
     */
    protected function safeTruncate(string $modelClass): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
            $modelClass::query()->truncate();
            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            $modelClass::query()->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }
}
