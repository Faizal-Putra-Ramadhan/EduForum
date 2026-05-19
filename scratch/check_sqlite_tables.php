<?php
try {
    $db = new PDO('sqlite:eduforum');
    $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables in SQLite file 'eduforum':" . PHP_EOL;
    foreach ($tables as $table) {
        echo $table . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
