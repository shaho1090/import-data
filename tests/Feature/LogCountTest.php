<?php

namespace Tests\Feature;

use App\Models\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LogCountTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    public function test_the_log_count_can_be_shown()
    {
        $numberOfRecords = rand(10, 50);

        Log::factory($numberOfRecords)->create();

        $this->getJson('/api/logs/count')->assertStatus(200)
            ->assertJsonFragment([
                "count" => $numberOfRecords
            ]);
    }

    public function test_the_log_count_can_be_filtered_with_service_name()
    {
        Log::factory(10)->create([
            'service_name' => 'order-service'
        ]);

        Log::factory(5)->create([
            'service_name' => 'invoice-service'
        ]);

        $this->json('GET', '/api/logs/count', [
            'serviceName' => 'order-service'
        ])->assertStatus(200)
            ->assertJsonFragment([
                "count" => 10
            ]);


        $this->json('GET', '/api/logs/count', [
            'serviceName' => 'invoice-service'
        ])->assertStatus(200)
            ->assertJsonFragment([
                "count" => 5
            ]);
    }


    public function test_the_log_count_can_be_filtered_with_status_code()
    {
        Log::factory(10)->create([
            'status_code' => 201
        ]);

        Log::factory(5)->create([
            'status_code' => 422
        ]);

        $this->json('GET', '/api/logs/count', [
            'statusCode' => 201
        ])->assertStatus(200)
            ->assertJsonFragment([
                "count" => 10
            ]);

        $this->json('GET', '/api/logs/count', [
            'statusCode' => 422
        ])->assertStatus(200)
            ->assertJsonFragment([
                "count" => 5
            ]);
    }

    public function test_the_log_count_can_be_filtered_by_start_date_and_end_date()
    {
        $date = Carbon::today()->subDays(100);

        $startDate = $date->toDateString();

        for ($i = 0; $i < 10; $i++) {
            Log::factory()->create([
                'date' => $date
            ]);

            $date->addDay();
        }

        $endDate = $date->toDateString();

        $date = Carbon::now()->subDays(200);

        for ($i = 0; $i < 30; $i++) {
            Log::factory()->create([
                'date' => $date
            ]);

            $date->addDay();
        }

        $this->json('GET', '/api/logs/count', [
            'startDate' => $startDate,
            'endDate' => $endDate
        ])->assertJsonFragment([
                "count" => 10
            ]);
    }
}
