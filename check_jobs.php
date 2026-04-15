<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$jobs = DB::table('jobs')->get();
foreach ($jobs as $j) {
    $p = json_decode($j->payload);
    echo $j->id . ' | ' . $p->displayName . ' | available_at: ' . date('Y-m-d H:i:s', $j->available_at) . PHP_EOL;
}
