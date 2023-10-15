<?php

$host = 'db';  // Docker Composeのサービス名
$dbname = 'mydatabase';
$username = 'user';
$password = 'password';
global $db;

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
} catch(PDOException $e) {
    $db_error_message = "Connection failed: " . $e->getMessage(); //エラーメッセージを出力せずに変数に格納する
    echo $db_error_message;
    exit;
}
?>