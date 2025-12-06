<?php

use Illuminate\Container\Container;

if (!function_exists('app')) {
    function app($abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return Container::getInstance();
        }

        return Container::getInstance()->make($abstract, $parameters);
    }
}

if (!function_exists('base_path')) {
    function base_path($path = '')
    {
        $app = app();
        if (method_exists($app, 'basePath')) {
            return $app->basePath($path);
        }

        // Fallback if app() is raw Container or not initialized
        $base = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2);

        return $base . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!function_exists('database_path')) {
    function database_path($path = '')
    {
        return base_path('database' . ($path ? DIRECTORY_SEPARATOR . $path : $path));
    }
}

if (!function_exists('config_path')) {
    function config_path($path = '')
    {
        return base_path('config' . ($path ? DIRECTORY_SEPARATOR . $path : $path));
    }
}

if (!function_exists('public_path')) {
    function public_path($path = '')
    {
        return base_path('public' . ($path ? DIRECTORY_SEPARATOR . $path : $path));
    }
}

if (!function_exists('resource_path')) {
    function resource_path($path = '')
    {
        return base_path('resources' . ($path ? DIRECTORY_SEPARATOR . $path : $path));
    }
}

if (!function_exists('storage_path')) {
    function storage_path($path = '')
    {
        return base_path('storage' . ($path ? DIRECTORY_SEPARATOR . $path : $path));
    }
}

if (!function_exists('event')) {
    function event(...$args)
    {
        return app(\Illuminate\Events\Dispatcher::class)->dispatch(...$args);
    }
}

if (!function_exists('dispatch')) {
    function dispatch($job)
    {
        return app(\Illuminate\Contracts\Bus\Dispatcher::class)->dispatch($job);
    }
}

if (!function_exists('jsonResponse')) {
    function jsonResponse($data, $statusCode = 200)
    {
        @header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}

if (!function_exists('jsonBody')) {
    function jsonBody()
    {
        return json_decode(file_get_contents('php://input'), true);
    }
}

if (!function_exists('now')) {
    function now()
    {
        return \Carbon\Carbon::now();
    }
}