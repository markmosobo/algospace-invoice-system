<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CyberRequest;
use Illuminate\Support\Facades\Storage;

class CleanupCyberRequests extends Command
{
    protected $signature = 'cyber:cleanup';

    protected $description = 'Clean up cancelled (1 day) and completed (30 days) cyber requests and delete uploaded files';

    public function handle()
    {
        $this->info("🚀 Starting Cyber Requests cleanup...");

        /*
        |----------------------------------------------------
        | CANCELLED REQUESTS (1 DAY OLD)
        |----------------------------------------------------
        */
        $cancelledQuery = CyberRequest::with('files')
            ->where('status', 'cancelled')
            ->where('updated_at', '<', now()->subDay());

        /*
        |----------------------------------------------------
        | COMPLETED REQUESTS (30 DAYS OLD)
        |----------------------------------------------------
        */
        $completedQuery = CyberRequest::with('files')
            ->where('status', 'completed')
            ->where('updated_at', '<', now()->subDays(30));

        if (!$cancelledQuery->exists() && !$completedQuery->exists()) {
            $this->info("✅ No requests to clean.");
            return 0;
        }

        $deleted = 0;

        // Helper function (avoid duplication)
        $deleteRequest = function ($request) use (&$deleted) {

            $this->info("Deleting Request ID: {$request->id} ({$request->status})");

            foreach ($request->files as $file) {

                if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                    Storage::disk('public')->delete($file->file_path);
                    $this->info(" - Deleted file: {$file->file_name}");
                }

                $file->delete();
            }

            $request->delete();
            $deleted++;
        };

        /*
        |----------------------------------------------------
        | PROCESS CANCELLED
        |----------------------------------------------------
        */
        $cancelledQuery->chunk(100, function ($requests) use ($deleteRequest) {
            foreach ($requests as $request) {
                $deleteRequest($request);
            }
        });

        /*
        |----------------------------------------------------
        | PROCESS COMPLETED
        |----------------------------------------------------
        */
        $completedQuery->chunk(100, function ($requests) use ($deleteRequest) {
            foreach ($requests as $request) {
                $deleteRequest($request);
            }
        });

        $this->info("🎯 Cleanup complete. Total deleted: {$deleted}");

        return 0;
    }
}