<?php

namespace App\DTO;

class StoreFileDTO
{
    public function __construct(
        public  readonly string $file,
        public  readonly string $fileName,
        public  readonly string $fileExtension,
        public  readonly string $fileMimeType,
        public  readonly int $chunkIndex,
        public  readonly int $totalChunks)
    {
    }
}
