<?php
$host = 'localhost';
$db   = 'taskflow_db';
$user = 'root';
$pass = ''; // leave blank if you haven't set a XAMPP MySQL password

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>