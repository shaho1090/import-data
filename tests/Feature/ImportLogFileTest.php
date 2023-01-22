<?php

use App\Jobs\ImportTextLogFileJob;
use App\Services\FileService\TextLogFileParserService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ImportLogFileTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    public function test_create_log_file_console_command()
    {
        $fileName = Str::random();

        $numberOfLines = rand(100, 1000);

        $this->artisan('create:log-file "' . $fileName . '" ' . $numberOfLines)->assertSuccessful();

        $fileName = $fileName . ".txt";

        Storage::assertExists("/logFiles/$fileName");
    }

    public function test_console_command_for_importing_log_file()
    {
        $fileName = $this->makeLogFile();

        $this->artisan('import:from-text ' . basename($fileName))->assertSuccessful();
    }

    public function test_get_error_when_importing_nonexistence_file()
    {
        $fileName = Str::random();

        $this->artisan('import:from-text ' . basename($fileName))->assertFailed();
    }

    public function test_job_is_dispatched_after_running_console_command()
    {
        Queue::fake(ImportTextLogFileJob::class);

        $fileName = Str::random();

        $fileWithPath = $this->makeLogFile($fileName, 2);

        $this->artisan('import:from-text ' . basename($fileWithPath))->assertSuccessful();

        Queue::assertPushed(ImportTextLogFileJob::class);
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_data_is_inserted_after_running_console_command()
    {
        $queueManager = app()->make(QueueManager::class);

        $defaultDriver = $queueManager->getDefaultDriver();

        $queueManager->setDefaultDriver('sync');

        $fileName = Str::random();

        $fileWithPath = $this->makeLogFile($fileName, 2);

        $lines = (new TextLogFileParserService($fileWithPath, 0, 1))->getLines();

        $this->assertDatabaseCount('logs', 0);

        $this->artisan('import:from-text ' . basename($fileWithPath));

        $this->assertDatabaseCount('logs', 2);
        $this->assertDatabaseHas('logs', [
            "service_name" => $lines[0]["service_name"],
            "date" => $lines[0]["date"],
            "http_verb" => $lines[0]["http_verb"],
            "path" => $lines[0]["path"],
            "http_protocol" => $lines[0]["http_protocol"],
            "status_code" => $lines[0]["status_code"],
        ]);

        $this->assertDatabaseHas('logs', [
            "service_name" => $lines[1]["service_name"],
            "date" => $lines[1]["date"],
            "http_verb" => $lines[1]["http_verb"],
            "path" => $lines[1]["path"],
            "http_protocol" => $lines[1]["http_protocol"],
            "status_code" => $lines[1]["status_code"],
        ]);

        $queueManager->setDefaultDriver($defaultDriver);
    }

    private function makeLogFile($fileName = '', $numberOfLines = 0): string
    {
        if (empty($fileName)) {
            $fileName = Str::random(20);
        }

        if ($numberOfLines == 0) {
            $numberOfLines = rand(10, 100);
        }

        $this->artisan('create:log-file "' . $fileName . '" ' . $numberOfLines);

        return Storage::path('/logFiles/' . $fileName . ".txt");
    }
}
