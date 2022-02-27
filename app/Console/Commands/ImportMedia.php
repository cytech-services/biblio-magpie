<?php

namespace App\Console\Commands;

use App\Jobs\ImportBook;
use App\Models\User;
use App\Notifications\Library\ImportBooksBatch;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Notification;
use Throwable;

class ImportMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the import disk for new media and import';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $storagePath = storage_path('app/import');
        // $files = File::allFiles($storagePath);
        $files = File::allFiles($storagePath);
        // dump($files);

        // Create Jobs for Batch
        $jobs = [];
        foreach ($files as $key => $file) {
            if (in_array($file->getExtension(), ['epub', 'pdf'])) {
                $jobs[] = new ImportBook([
                    'realPath' => $file->getRealPath(),
                    'relativePath' => str_replace($storagePath, '.', $file->getRealPath()),
                    'filename' => $file->getFilename(),
                    'extension' => $file->getExtension(),
                    'size' => $file->getSize(),
                ]);

                continue;
            }

            // Unset the file from the array to free up memory
            unset($files[$key]);
        }

        $batch = Bus::batch([
            $jobs
        ])->then(function (Batch $batch) {
            // All jobs completed successfully...
            Notification::send(User::all(), new ImportBooksBatch([
                'batch_id' => $batch->id,
                'name' => $batch->name,
                'status' => 'Import Complete!',
                'progress' => 100,
            ]));
        })->catch(function (Batch $batch, Throwable $e) {
            // First batch job failure detected...
            Notification::send(User::all(), new ImportBooksBatch([
                'batch_id' => $batch->id,
                'name' => $batch->name,
                'status' => 'FAILED. Check the logs for more details.',
                'progress' => 100,
            ]));
        })->finally(function (Batch $batch) {
            // The batch has finished executing...
        })->name('Import Books')->onQueue('import')->dispatch();

        return 0;
    }
}
