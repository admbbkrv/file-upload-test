<?php

namespace App\Services\FileService\Interfaces;

interface FileInfoInterface
{
    public function getFileInfo(string $pathFile): array;
    public function getFileExtension(string $pathFile): string;
}
