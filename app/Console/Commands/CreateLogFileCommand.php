<?php

namespace App\Console\Commands;

use Carbon\Carbon;
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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /** In case you may encounter memory limitation for a very large file **/
        // ini_set('memory_limit', '13g');

        $this->date = Carbon::now()->subMonths(10);

        $array = [];

        for ($i = 1; $i <= $this->argument('numberOfLines'); $i++) {
            $array[] =  $this->getRandomLineData();
        }

        Storage::append('/logFiles/'.$this->argument('fileName').'.txt', implode("\n", $array));

        $this->info('The file was created in this path:/storage/app/logFiles');

        return Command::SUCCESS;
    }

    private function getRandomLineData(): string
    {
        $status = collect([201, 422]);

        $status->random();

        $services = collect([
            'order' => [
                'service-name' => 'order-service',
                'path' => '/orders'
            ],
            'invoice' => [
                'service-name' => 'invoice-service',
                'path' => '/invoices'
            ]
        ]);

        $date = $this->date->addSeconds(5)->format("d/M/Y:H:i:s");
        $service = $services->random();

        return
            $service['service-name'] . ' ' . "-" . ' [' . $date . '] ' . '"' . 'POST ' . $service['path']
            . ' HTTP/1.1' . '" ' . $status->random();
    }
}
