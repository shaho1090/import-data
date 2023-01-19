<?php

namespace App\Jobs;

use App\Models\Log;
use Carbon\Carbon;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
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

    private string $path;

    public int $tries = 2;

    public int $timeout = 3600;

    /**
     * Indicate if the job should be marked as failed on timeout.
     *
     * @var bool
     */
    public bool $failOnTimeout = true;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $path)
    {
        $this->path = $path;

        $this->onQueue(self::QUEUE);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $file = new SplFileObject($this->path,'rb');

        while ($file->current()) {
            $arrayLine = explode(' ', $file->current());
            $arrayLine[2] =
                Carbon::parse(
                    DateTime::createFromFormat("d-M-Y H i s",$this->formatDate($arrayLine[2]))
                )->toDateTimeString();

            $arrayLine[3] = str_replace('"', '', $arrayLine[3]);
            $arrayLine[5] = str_replace('"', '', $arrayLine[5]);
            $arrayLine[6] = strval(intval($arrayLine[6]));

            InsertLogToDatabaseJob::dispatch([
                'service_name' => $arrayLine[0],
                'date' => $arrayLine[2],
                'http_verb' => $arrayLine[3],
                'path' => $arrayLine[4],
                'http_protocol' => $arrayLine[5],
                'status_code' => $arrayLine[6],
                'created_at' => now()->toDateString(),
                'updated_at' => now()->toDateTimeString()
            ]);

            $file->next();

//            dump(memory_get_usage());
        }
    }

    public function formatDate(string $date): array|string
    {
        $formedDate = str_replace(']', '', str_replace('[', '', $date));

        $formedDate = str_replace('/','-',$formedDate);

        return str_replace(':', ' ', $formedDate);
    }
}
