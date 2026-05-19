<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$missingMigrations = [
    '2026_03_31_090405_create_conversations_table',
    '2026_03_31_090405_create_messages_table',
    '2026_03_31_090405_create_notification_logs_table',
    '2026_04_05_041654_update_conversations_for_groups'
];

echo "Removing records from migrations table..." . PHP_EOL;

foreach ($missingMigrations as $migration) {
    $deleted = DB::table('migrations')->where('migration', $migration)->delete();
    if ($deleted) {
        echo "Removed: $migration" . PHP_EOL;
    } else {
        echo "Not found (already removed?): $migration" . PHP_EOL;
    }
}

echo "Cleanup complete. Now you can run 'php artisan migrate'." . PHP_EOL;
