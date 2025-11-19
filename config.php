<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$DB_HOST = "localhost";
$DB_NAME = "kalenteri";
$DB_USER = "root";
$DB_PASS = ""; // XAMPP often empty

try {
  $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  http_response_code(500);
  die("DB error: " . $e->getMessage());
}
