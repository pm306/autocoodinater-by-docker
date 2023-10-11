<?php

$host = 'db';  // Docker Composeのサービス名
$dbname = 'mydatabase';
$username = 'user';
$password = 'password';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
} catch(PDOException $e) {
    $db_error = "Connection failed: " . $e->getMessage(); //エラーメッセージを出力せずに変数に格納する
}
?>