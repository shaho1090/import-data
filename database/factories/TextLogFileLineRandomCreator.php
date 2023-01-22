<?php

namespace Database\Factories;

use Carbon\Carbon;

class TextLogFileLineRandomCreator
{
    private Carbon $date;
    private string $line;
    private array $arrayLine;

    public function __construct()
    {
        $this->date = Carbon::now()->subMonths(10);
    }

    public function create(): static
    {
        $statusCodes = collect([201, 422]);
        $statusCode = $statusCodes->random();

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

        $this->arrayLine = [
            "service_name" => $service['service-name'],
            "date" => $date,
            "http_verb" => "POST",
            "path" =>  $service['path'],
            "http_protocol" => "HTTP/1.1",
            "status_code" => $statusCode,
        ];

        $this->line = $service['service-name'] . ' ' . "-" . ' [' . $date . '] ' . '"' . 'POST ' . $service['path']
            . ' HTTP/1.1' . '" ' . $statusCode;

        return $this;
    }

    public function getString(): string
    {
        return $this->line;
    }

    public function getArray(): array
    {
        return $this->arrayLine;
    }
}
