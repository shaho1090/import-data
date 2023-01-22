<?php

namespace App\Services\FileService;

use SplFileObject;

abstract class AbstractFileService
{
    protected string $filePath;
    protected int $startLine;
    protected int $endLine;
    protected array $lines = [];
    protected SplFileObject $file;

    public function __construct($filePath, $startLine, $endLine)
    {
        $this->filePath = $filePath;
        $this->startLine = $startLine;
        $this->endLine = $endLine;
        $this->parse();
    }

    abstract protected function parse(): void;

    public function getLines(): array
    {
        return $this->lines;
    }

    public function areThereAnyLines(): string|bool
    {
        return $this->file->current();
    }
}
