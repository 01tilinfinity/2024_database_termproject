<?php
session_start();
include 'config.php';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $dbid, $dbpass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>
