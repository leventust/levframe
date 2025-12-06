<?php

use Core\Http\Kernel;

// Bootstrap Application with all service providers
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Initialize HTTP Kernel
$kernel = new Kernel($app);

// Handle Request
$kernel->handle();
