<?php
$host = 'localhost';
$user = 'root';
$passwords = ['', 'root', 'yoga2005', 'password', '12345678'];

foreach ($passwords as $pass) {
    try {
        $pdo = new PDO("mysql:host=$host", $user, $pass);
        echo "SUCCESS: $pass";
        exit;
    } catch (PDOException $e) {
        echo "FAIL ($pass): " . $e->getMessage() . "\n";
    }
}
