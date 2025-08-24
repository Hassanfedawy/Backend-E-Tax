<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class CreateDatabase extends Command
{
    protected $signature = 'db:create';
    protected $description = 'Create database if it does not exist';

   public function handle()
{
    $database = Config::get('database.connections.mysql.database');
    $charset = Config::get('database.connections.mysql.charset', 'utf8mb4');
    $collation = Config::get('database.connections.mysql.collation', 'utf8mb4_unicode_ci');

    // ❌ Wrong → Laravel still tries to connect to etax
    // Config::set('database.connections.mysql.database', null);

    // ✅ Correct → force Laravel to connect without selecting DB
    Config::set('database.connections.mysql.database', null);

    DB::purge('mysql'); // clear old connection
    DB::reconnect('mysql'); // reconnect without DB selected

    $query = "CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET $charset COLLATE $collation;";

    DB::statement($query);

    $this->info("Database `$database` created or already exists.");
}

}
