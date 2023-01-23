<?php

namespace App\Jobs;

use App\Services\Concretes\LogService;
use App\Services\FileService\TextLogFileParserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportTextLogFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public const QUEUE = 'import-text-log-file';
    public const NUMBER_OF_LINES = 10;

    private string $path;

    public int $tries = 3;

    public int $timeout = 300;
    private int $startLine;
    private int $endLine;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $path, $startLine = 0)
    {
        $this->path = $path;
        $this->startLine = $startLine;
        $this->endLine = $startLine + self::NUMBER_OF_LINES;

        $this->onQueue(self::QUEUE);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $fileService = new TextLogFileParserService($this->path, $this->startLine, $this->endLine);

        $this->insertLines($fileService->getLines());

        if ($fileService->areThereAnyLines()) {
            self::dispatch($this->path, $this->endLine + 1);
        }
    }

    private function insertLines($lines)
    {
        (new LogService())->insertMany($lines);
    }
}
