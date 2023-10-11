<?php

namespace App\Services\FileService;

use App\Services\FileService\Interfaces\DeleteFileInterface;
use Illuminate\Support\Facades\Storage;
use Throwable;

class DeleteFileService implements DeleteFileInterface
{

    public function deleteFile(string $pathFile): bool
    {
        try {
            if (! Storage::exists($pathFile)) {
                return false;
            }
            return Storage::delete($pathFile);
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }

    public function deleteDirectory(string $pathDirectory): bool
    {
        try {
            if (! Storage::directoryExists($pathDirectory)) {
                return false;
            }
            return Storage::deleteDirectory($pathDirectory);
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }
}
