<?php

namespace App\Services\FileService;

use Carbon\Carbon;
use DateTime;
use SplFileObject;

class TextLogFileParserService extends AbstractFileService
{
    protected function parse(): void
    {
        $this->file = new SplFileObject($this->filePath, 'rb');

        for ($i = $this->startLine; $i <= ($this->endLine + 1); $i++) {
            $this->file->seek($i);

            if ((!$this->file->current() || $i == ($this->endLine + 1))) {
                break;
            }

            $this->lines[] = $this->getPreparedLineToInsert($this->file->current());
        }
    }

    private function formatDate(string $date): array|string
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
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString()
        ];
    }
}
