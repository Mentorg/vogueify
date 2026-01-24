<?php

namespace App\Jobs\Product;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class DeleteProductImageJob implements ShouldQueue
{
    use Queueable;

    public array $imagePaths;

    /**
     * Create a new job instance.
     */
    public function __construct(array $imagePaths)
    {
        $this->imagePaths = $imagePaths;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->imagePaths as $path) {
            if (!$path) {
                continue;
            }

            $relativePath = str_replace('/storage/', '', $path);

            if (Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
            }
        }
    }
}
