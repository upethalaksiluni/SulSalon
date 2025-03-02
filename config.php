<?php
$host = 'localhost';
$db = 'salon';
$user = 'root';
$pass = 'password';

$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
