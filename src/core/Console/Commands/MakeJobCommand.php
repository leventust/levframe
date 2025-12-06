<?php

namespace Core\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeJobCommand extends Command
{
    protected $signature = 'make:job {name : The name of the job} {--sync : Indicates that job should be synchronous}';

    protected $description = 'Create a new job class';

    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $name = $this->argument('name');

        // Simple stub
        $stub = <<<'EOT'
<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class {{ class }} implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    // Dispatchable trait is in Foundation, often not available without it.
    // So we skip it or mock it if needed. 
    // Actually Dispatchable is handy. If we lack it, we use dispatch() helper or Queue::push.

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }
}
EOT;
        // Fix namespace/classname
        $className = Str::studly($name);
        $content = str_replace('{{ class }}', $className, $stub);

        $path = $this->laravel->basePath('src/app/Jobs/' . $className . '.php');

        $this->makeDirectory($path);

        $this->files->put($path, $content);

        $this->info('Job created successfully.');

        return self::SUCCESS;
    }

    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }
}
