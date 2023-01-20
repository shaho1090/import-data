<?php

namespace App\Jobs;

use Carbon\Carbon;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use SplFileObject;

class ImportDataFromTextFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public const QUEUE = 'import:data';
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
        $file = new SplFileObject($this->path, 'rb');

        $lines = [];

        for ($i = $this->startLine; $i <= $this->endLine; $i++) {
            $file->seek($i);

            if ((!$file->current() || $i == $this->endLine) && !empty($lines)) {
                $this->insertLines($lines);
                break;
            }

            $lines[] = $this->getPreparedLineToInsert($file->current());
        }

        if ($file->current()) {
            self::dispatch($this->path, $this->endLine);
        }
    }

    public function formatDate(string $date): array|string
    {
        $formedDate = str_replace(']', '', str_replace('[', '', $date));

        $formedDate = str_replace('/', '-', $formedDate);

        return str_replace(':', ' ', $formedDate);
    }

    private function getLineInArray(string $line): array
    {
        $arrayLine = explode(' ', $line);
        $arrayLine[2] =
            Carbon::parse(
                DateTime::createFromFormat("d-M-Y H i s", $this->formatDate($arrayLine[2]))
            )->toDateTimeString();

        $arrayLine[3] = str_replace('"', '', $arrayLine[3]);
        $arrayLine[5] = str_replace('"', '', $arrayLine[5]);
        $arrayLine[6] = strval(intval($arrayLine[6]));

        return $arrayLine;
    }

    private function insertLines($lines)
    {
        DB::table('logs')->insert($lines);
    }

    private function getPreparedLineToInsert(string $current): array
    {
        $arrayLine = $this->getLineInArray($current);

        return [
            'service_name' => $arrayLine[0],
            'date' => $arrayLine[2],
            'http_verb' => $arrayLine[3],
            'path' => $arrayLine[4],
            'http_protocol' => $arrayLine[5],
            'status_code' => $arrayLine[6],
            'created_at' => now()->toDateString(),
            'updated_at' => now()->toDateTimeString()
        ];
    }
}
