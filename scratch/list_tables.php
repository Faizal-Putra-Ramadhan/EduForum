<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = DB::select('SHOW TABLES');
$database = config('database.connections.mysql.database');
$columnName = "Tables_in_" . $database;

foreach ($tables as $table) {
    echo $table->$columnName . PHP_EOL;
}
