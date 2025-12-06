<?php

namespace Core;

use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;


class Application extends Container implements ApplicationContract
{
    protected $basePath;

    public function __construct($basePath = null)
    {
        if ($basePath) {
            $this->setBasePath($basePath);
        }
        $this->registerBaseBindings();
        $this->registerCoreContainerAliases();
        $this->registerEncrypter();
    }

    protected function registerEncrypter()
    {
        $this->singleton('encrypter', function ($app) {
            $key = env('APP_KEY');
            $cipher = env('APP_CIPHER', 'AES-256-CBC');

            if (str_starts_with($key, 'base64:')) {
                $key = base64_decode(substr($key, 7));
            }

            if (empty($key)) {
                throw new \RuntimeException('No application encryption key has been specified.');
            }

            return new \Illuminate\Encryption\Encrypter($key, $cipher);
        });
    }

    public function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, '\/');
        $this->bindPathsInContainer();
        return $this;
    }

    protected function bindPathsInContainer()
    {
        $this->instance('path', $this->path());
        $this->instance('path.base', $this->basePath());
        $this->instance('path.config', $this->configPath());
        $this->instance('path.database', $this->databasePath());
        $this->instance('path.public', $this->publicPath());
        $this->instance('path.resources', $this->resourcePath());
        $this->instance('path.storage', $this->storagePath());
    }

    public function path($path = '')
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'app' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    public function basePath($path = '')
    {
        return $this->basePath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    public function configPath($path = '')
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'config' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    public function databasePath($path = '')
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'database' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    public function storagePath($path = '')
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'storage' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    public function resourcePath($path = '')
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'resources' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    public function publicPath($path = '')
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'public' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    public function version()
    {
        return '1.0.0';
    }

    public function environment(...$environments)
    {
        if (count($environments) > 0) {
            $patterns = is_array($environments[0]) ? $environments[0] : $environments;
            foreach ($patterns as $pattern) {
                if ($pattern === env('APP_ENV')) {
                    return true;
                }
            }
            return false;
        }

        return env('APP_ENV', 'production');
    }

    public function isDownForMaintenance()
    {
        return false;
    }

    public function registerConfiguredProviders()
    {
        // NOOP or implement load from config
    }

    public function register($provider, $options = [], $force = false)
    {
        if (is_string($provider)) {
            $provider = new $provider($this);
        }

        if (method_exists($provider, 'register')) {
            $provider->register();
        }

        // Return provider?
        return $provider;
    }

    public function boot()
    {
        // Boot logic handled manually or here
    }

    public function bootstrapPath($path = '')
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'bootstrap' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    public function langPath($path = '')
    {
        return $this->resourcePath() . DIRECTORY_SEPARATOR . 'lang' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    public function hasDebugModeEnabled()
    {
        return env('APP_DEBUG', false);
    }

    public function runningInConsole()
    {
        return php_sapi_name() === 'cli' || php_sapi_name() === 'phpdbg';
    }

    public function runningUnitTests()
    {
        return env('APP_ENV') === 'testing';
    }

    public function maintenanceMode()
    {
        // Minimal stub
        return new class {
            public function active()
            {
                return false;
            }
        };
    }

    public function booting($callback)
    {
    }
    public function booted($callback)
    {
    }

    public function registerDeferredProvider($provider, $service = null)
    {
    }
    public function resolveProvider($provider)
    {
        return new $provider($this);
    }

    public function terminating($callback)
    {
    }
    public function terminate()
    {
    }

    protected function registerBaseBindings()
    {
        static::setInstance($this);
        $this->instance('app', $this);
        $this->instance(Container::class, $this);
        $this->instance(ApplicationContract::class, $this); // Bind interface
        $this->instance(\Illuminate\Contracts\Container\Container::class, $this);
        $this->instance(Container::class, $this);
    }

    protected function registerCoreContainerAliases()
    {
        // Mapping common aliases used by Laravel
        $aliases = [
            'app' => [ApplicationContract::class, Container::class, self::class],
            'config' => [\Illuminate\Config\Repository::class],
            'events' => [\Illuminate\Events\Dispatcher::class, \Illuminate\Contracts\Events\Dispatcher::class],
            'files' => [\Illuminate\Filesystem\Filesystem::class],
            'db' => [\Illuminate\Database\DatabaseManager::class, \Illuminate\Database\ConnectionResolverInterface::class],
            'cache' => [\Illuminate\Cache\CacheManager::class, \Illuminate\Contracts\Cache\Factory::class],
            'cache.store' => [\Illuminate\Cache\Repository::class, \Illuminate\Contracts\Cache\Repository::class, \Illuminate\Contracts\Cache\Store::class],
            'queue' => [\Illuminate\Queue\QueueManager::class, \Illuminate\Contracts\Queue\Factory::class, \Illuminate\Contracts\Queue\Monitor::class],
            'queue.connection' => [\Illuminate\Contracts\Queue\Queue::class],
            'queue.worker' => [\Illuminate\Queue\Worker::class],
            'queue.listener' => [\Illuminate\Queue\Listener::class],
            'queue.failer' => [\Illuminate\Queue\Failed\FailedJobProviderInterface::class],
            'composer' => [\Illuminate\Support\Composer::class],
            'encrypter' => [\Illuminate\Encryption\Encrypter::class, \Illuminate\Contracts\Encryption\Encrypter::class],
        ];

        foreach ($aliases as $key => $aliasList) {
            foreach ($aliasList as $alias) {
                $this->alias($key, $alias);
            }
        }
    }

    // Missing methods from ApplicationContract 
    // Implementation of other interface methods as minimal stubs...
    // Detailed implementation is verbose, I'll implement required ones as I hit errors.
    // Abstract methods from ApplicationContract:
    public function bootstrapWith(array $bootstrappers)
    {
    }
    public function detectEnvironment(\Closure $callback)
    {
        return 'production';
    }
    public function environmentFile()
    {
        return '.env';
    }
    public function environmentFilePath()
    {
        return $this->basePath() . '/.env';
    }
    public function getCachedConfigPath()
    {
        return '';
    }
    public function getCachedServicesPath()
    {
        return '';
    }
    public function getCachedPackagesPath()
    {
        return '';
    }
    public function getNamespace()
    {
        return 'App\\';
    }
    public function getProviders($provider)
    {
        return [];
    }
    public function hasBeenBootstrapped()
    {
        return true;
    }
    public function loadDeferredProviders()
    {
    }
    public function setLocale($locale)
    {
    }
    public function getLocale()
    {
        return 'en';
    }
    public function shouldSkipMiddleware()
    {
        return false;
    }

    // Container methods are inherited
}
