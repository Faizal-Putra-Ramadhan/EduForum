<?php
$host = 'localhost';
$user = 'root';
$pass = 'yoga2005';
$db   = 'eduforum';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "DATABASE_READY";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
