<?php

use Illuminate\Container\Container;
use Illuminate\Config\Repository as Config;
use Illuminate\Support\Facades\Facade;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Cache\CacheServiceProvider;
use Illuminate\Bus\BusServiceProvider;
use Illuminate\Queue\QueueServiceProvider;
use Illuminate\Support\ServiceProvider;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/env.php';
require_once __DIR__ . '/../src/core/Helpers.php';

// Create Application
$app = new \Core\Application(dirname(__DIR__));

// Enable Facades
Facade::setFacadeApplication($app);

// Bind Composer
$app->singleton('composer', function ($app) {
    return new \Illuminate\Support\Composer($app['files']);
});

// Bind ExceptionHandler
$app->singleton(
    \Illuminate\Contracts\Debug\ExceptionHandler::class,
    function () {
        return new class implements \Illuminate\Contracts\Debug\ExceptionHandler {
            public function report(\Throwable $e)
            {
                // Optional: log exception
                error_log($e->getMessage());
            }
            public function render($request, \Throwable $e)
            {
                echo $e->getMessage();
            }
            public function renderForConsole($output, \Throwable $e)
            {
                (new \Symfony\Component\Console\Application)->renderThrowable($e, $output);
            }
            public function shouldReport(\Throwable $e)
            {
                return true;
            }
        };
    }
);

// Register Config
// $app->instance('config', ...); // Already bound by registerBaseBindings? No, we configure it manually.
$app->instance('config', $config = new Config([
    'database' => require __DIR__ . '/../config/database.php',
    'queue' => require __DIR__ . '/../config/queue.php',
    'cache' => require __DIR__ . '/../config/cache.php',
    'logging' => require __DIR__ . '/../config/logging.php',
]));

// Bind View Service
$app->singleton('view', function () {
    $engine = new \League\Plates\Engine(resource_path('views'));
    return new \Core\View($engine);
});

// Register View Alias
class_alias(\Core\Facades\View::class, 'View');

// Bind HTTP Service
$app->singleton('http', function () {
    return new \Core\Http\Client();
});

// Register HTTP Alias
class_alias(\Core\Facades\Http::class, 'Http');

// Path bindings are handled by Application::setBasePath

// Use a simple class for Application contract if needed by some providers
// But standard Container often suffices if we bind 'app'.

// Register Service Providers
$providers = [
    EventServiceProvider::class,
    FilesystemServiceProvider::class,
    DatabaseServiceProvider::class,
    \Illuminate\Database\MigrationServiceProvider::class,
    CacheServiceProvider::class,
    \Illuminate\Log\LogServiceProvider::class,
    BusServiceProvider::class,
    QueueServiceProvider::class,
];

foreach ($providers as $providerClass) {
    if (class_exists($providerClass)) {
        $provider = new $providerClass($app);
        // Only register if method exists (some are deferrable but we run immediately)
        if (method_exists($provider, 'register')) {
            $provider->register();
        }
        // Save for booting
        $loadedProviders[] = $provider;
    }
}

// Boot Providers
foreach ($loadedProviders as $provider) {
    if (method_exists($provider, 'boot')) {
        $app->call([$provider, 'boot']);
    }
}

return $app;
