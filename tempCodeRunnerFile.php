<?php
// conf.php 
declare(strict_types=1);
session_start();

// === DB config 
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', 'Ivan_awesome_2025');
define('DB_NAME', 'project_db');
define('DB_PORT', 3306);

// === error reporting - set display_errors = Off on production ===
error_reporting(E_ALL);
ini_set('display_errors', '1'); // set to 0 on production

// === create mysqli connection with charset ===
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
$conn->set_charset('utf8mb4');

// simple helper for safe redirect
function redirect_to(string $url) {
    header('Location: ' . $url);
    exit;
}