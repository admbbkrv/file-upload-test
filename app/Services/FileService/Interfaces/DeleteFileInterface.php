<?php

namespace App\Services\FileService\Interfaces;

interface DeleteFileInterface
{
    public function deleteFile(string $pathFile): bool;
    public function deleteDirectory(string $pathDirectory): bool;
}
