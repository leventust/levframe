<?php
use Dotenv\Dotenv;

$basePath = dirname(__DIR__);

define("BASE_PATH", $basePath);

$dotenv = Dotenv::createImmutable($basePath);
$dotenv->load();

date_default_timezone_set(env('TIMEZONE', 'Europe/Istanbul'));

if (env('APP_DEBUG', false) == true) {
    ini_set('display_errors', E_ALL);
    ini_set('display_startup_errors', E_ALL);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

// Sadece web istekleri için header gönder (CLI'da çalışmaz)
if (php_sapi_name() !== 'cli' && !empty($_SERVER['REQUEST_METHOD'])) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, token');

    // CORS preflight handling
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}