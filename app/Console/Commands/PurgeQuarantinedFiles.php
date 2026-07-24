<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PurgeQuarantinedFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:purge-quarantine';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete files in quarantine older than 30 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = 0;
        $quarantinePath = 'quarantine';
        
        // Get all files in quarantine directory
        $files = Storage::allFiles($quarantinePath);
        
        foreach ($files as $file) {
            // Get last modified time
            $lastModified = Storage::lastModified($file);
            $daysOld = (time() - $lastModified) / (60 * 60 * 24);
            
            if ($daysOld > 30) {
                Storage::delete($file);
                $count++;
            }
        }
        
        $message = "Purged {$count} file(s) from quarantine";
        $this->info($message);
        Log::info($message);
        
        return self::SUCCESS;
    }
}
