<?php
// config.php
$host = 'localhost:3306';
$user = 'root';
$password = '1234';
$db = 'db_service_ac';

$con = mysqli_connect($host, $user, $password, $db);

if (!$con) {
    die('Connection failed: ' . mysqli_connect_error());
}

mysqli_set_charset($con, 'utf8mb4');
?>
