<?php

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'employeemanagment';

$dsn = "mysql:host=$host;dbname=$db;charest=utf8mb4";


$option = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
];


try{

    $pdo = new PDO($dsn, $user, $pass, $option);

} catch(PDOException $e){
    echo 'Connection Failed:'. htmlspecialchars($e->getMessage());
}