<?php

namespace Core\Http;

use Buki\Router\Router;
use Core\Application;

class Kernel
{
    protected Application $app;
    protected Router $router;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->router = new Router([
            'paths' => [
                'controllers' => $this->app->path('Controllers'),
                'middlewares' => $this->app->path('Middleware'),
            ],
            'namespaces' => [
                'controllers' => 'App\Controllers',
                'middlewares' => 'App\Middleware',
            ],
            'debug' => $this->app->hasDebugModeEnabled(),
        ]);
    }

    public function handle()
    {
        $this->mapRoutes();
        $this->router->run();
    }

    protected function mapRoutes()
    {
        $router = $this->router;

        $routesPath = $this->app->path('routes.php');
        if (file_exists($routesPath)) {
            require $routesPath;
        } else {
            // Fallback or Try src/app/routes.php if app_path is strictly 'app'
            // Based on Application::path(), it returns {base}/app/{path}
            // So $this->app->path('routes.php') should be correct for src/app/routes.php
        }
    }
}
