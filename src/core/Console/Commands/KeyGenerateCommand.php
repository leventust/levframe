<?php

namespace Core\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Str;

class KeyGenerateCommand extends Command
{
    use ConfirmableTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'key:generate
                    {--show : Display the key instead of modifying files}
                    {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the application key';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $key = $this->generateRandomKey();

        if ($this->option('show')) {
            $this->line('<comment>' . $key . '</comment>');
            return;
        }

        // Setup .env file if not exists
        if (!file_exists(base_path('.env'))) {
            // Check if .env.example exists and copy
            if (file_exists(base_path('.env.example'))) {
                copy(base_path('.env.example'), base_path('.env'));
            }
        }

        if (!$this->setKeyInEnvironmentFile($key)) {
            return;
        }

        $this->info('Application key set successfully.');
    }

    /**
     * Generate a random key for the application.
     *
     * @return string
     */
    protected function generateRandomKey()
    {
        return 'base64:' . base64_encode(
            \Illuminate\Encryption\Encrypter::generateKey('AES-256-CBC')
        );
    }

    /**
     * Set the application key in the environment file.
     *
     * @param  string  $key
     * @return bool
     */
    protected function setKeyInEnvironmentFile($key)
    {
        $currentKey = $this->laravel['config']['app.key'] ?? env('APP_KEY');

        if (strlen($currentKey) !== 0 && (!$this->confirmToProceed())) {
            return false;
        }

        $this->writeNewEnvironmentFileWith($key);

        return true;
    }

    /**
     * Write a new environment file with the given key.
     *
     * @param  string  $key
     * @return void
     */
    protected function writeNewEnvironmentFileWith($key)
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            copy(base_path('.env.example'), $envPath);
        }

        $input = file_get_contents($envPath);

        $replaced = preg_replace(
            $this->keyReplacementPattern(),
            'APP_KEY=' . $key,
            $input
        );

        if ($replaced === $input || $replaced === null) {
            // Key might not exist, append it
            if (!str_contains($input, 'APP_KEY=')) {
                $replaced = $input . "\nAPP_KEY=" . $key . "\n";
            } else {
                $this->error('Unable to replace APP_KEY in .env file.');
                return;
            }
        }

        file_put_contents($envPath, $replaced);
    }

    /**
     * Get a regex pattern that will match env APP_KEY with any existing value.
     *
     * @return string
     */
    protected function keyReplacementPattern()
    {
        $escaped = preg_quote('=' . env('APP_KEY'), '/');

        return "/^APP_KEY{$escaped}/m";
    }
}
