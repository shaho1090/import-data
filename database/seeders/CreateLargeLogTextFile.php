<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CreateLargeLogTextFile extends Seeder
{
    private Carbon $date;

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->date = Carbon::now()->subMonths(10);

        $this->getRandomLineData();

        for ($i = 1; $i <= 1000; $i++) {
            Storage::append('/logFiles/log-file-3.txt', $this->getRandomLineData());
        }

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

        $date = $this->date->addSeconds(5)->format("d/M/Y H:i:s");
        $service = $services->random();

        return
            $service['service-name'] . ' ' . "-" . ' [' . $date . '] ' . '"' . 'POST ' . $service['path']
            . ' HTTP/1.1' . '" ' . $status->random();
    }

}
