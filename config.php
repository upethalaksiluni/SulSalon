<?php
$host = 'localhost';
$db = 'user_db';
$user = 'root';
$pass = 'password';

$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
