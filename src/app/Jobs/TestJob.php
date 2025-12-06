<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TestJob implements ShouldQueue
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