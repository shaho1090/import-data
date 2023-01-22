<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Database\Factories\TextLogFileLineRandomCreator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CreateLogFileCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:log-file {fileName} {numberOfLines}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a text log file for testing.';
    private Carbon $date;
    private TextLogFileLineRandomCreator $lineCreator;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /** In case you may encounter memory limitation for a very large file **/
        // ini_set('memory_limit', '13g');

        $this->lineCreator = new TextLogFileLineRandomCreator();

        $array = [];

        for ($i = 1; $i <= $this->argument('numberOfLines'); $i++) {
            $array[] =  $this->lineCreator->create()->getString();
        }

        Storage::append('/logFiles/'.$this->argument('fileName').'.txt', implode("\n", $array));

        $this->info('The file was created in this path:/storage/app/logFiles');

        return Command::SUCCESS;
    }
}
