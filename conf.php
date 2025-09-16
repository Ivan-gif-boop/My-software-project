<?php
// conf.php
declare(strict_types=1);

// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// === DB config ===
// Use defined() check to prevent "already defined" warnings
if (!defined('DB_HOST')) define('DB_HOST', '127.0.0.1');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', 'Ivan_awesome_2025');
if (!defined('DB_NAME')) define('DB_NAME', 'project_db');
if (!defined('DB_PORT')) define('DB_PORT', 3306);

// === error reporting - set display_errors = Off on production ===
error_reporting(E_ALL);
ini_set('display_errors', '1'); // set to 0 on production

// === create mysqli connection with charset ===
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = $conn ?? new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
$conn->set_charset('utf8mb4');

// Simple helper for safe redirect
if (!function_exists('redirect_to')) {
    function redirect_to(string $url): void {
        header('Location: ' . $url);
        exit;
    }
}
