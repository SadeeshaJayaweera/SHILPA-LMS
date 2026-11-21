<?php
declare(strict_types=1);

namespace App\Jobs;

use App\Queue\JobInterface;

class ProcessMediaUploadJob implements JobInterface {
    public string $filePath;
    public string $disk;

    public function __construct(string $filePath, string $disk = 'public') {
        $this->filePath = $filePath;
        $this->disk = $disk;
    }

    public function handle(): void {
        // Mock processing / image thumbnail pipeline
        if (file_exists($this->filePath)) {
            touch($this->filePath);
        }
    }
}
