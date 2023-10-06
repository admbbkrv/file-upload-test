<?php

namespace App\Services\FileService\Interfaces;

interface GetFileInterface
{
    public function getFile(string $pathFile);

    public function getFilesFromDirectory(string $pathDirectory);
}
