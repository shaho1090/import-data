<?php

namespace Database\Factories;

use App\Models\Log;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Log>
 */
class LogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $line = (new TextLogFileLineRandomCreator())->create()->getArray();

        return [
            "service_name" => $line['service_name'],
            "date" => $line["date"],
            "http_verb" => $line["http_verb"],
            "path" => $line["path"],
            "http_protocol" => $line["http_protocol"],
            "status_code" => $line["status_code"],
        ];
    }
}
