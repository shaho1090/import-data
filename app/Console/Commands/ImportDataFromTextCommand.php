<?php

namespace App\Console\Commands;

use App\Jobs\ImportDataFromTextFileJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportDataFromTextCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:from-text {logFile}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import log data from text file.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $fileName = $this->argument('logFile');

        if(!Storage::fileExists('/logFiles/'.$fileName)){
            $this->error('There is no such file in the logFiles directory!');
        }

        $path = Storage::path('/logFiles/'.$fileName);

        ImportDataFromTextFileJob::dispatch($path);

        $this->info('The file has been passed to the job to import ');

        return Command::SUCCESS;
    }
}
