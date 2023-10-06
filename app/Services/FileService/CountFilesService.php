<?php

namespace App\Services\FileService;

use App\Services\FileService\Interfaces\CountFilesInterface;
use Throwable;

class CountFilesService implements CountFilesInterface
{
    private GetFileService $getFileService;

    public function __construct(GetFileService $getFileService)
    {
        $this->getFileService = $getFileService;
    }

    public function countFiles(string $pathDirectory): int
    {
        try {
            $files = $this->getFiles($pathDirectory);
            return count($files);
        } catch (Throwable $throwable) {
            throw  $throwable;
        }
    }

    private function getFiles(string $pathDirectory): array
    {
        return $this->getFileService->getFilesFromDirectory($pathDirectory);
    }
}
