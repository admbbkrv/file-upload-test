<?php

namespace App\DTO;

class StoreChunkDTO
{
    public function __construct(
        public  readonly string $file,
        public  readonly string $fileName,
        public  readonly string $fileExtension,
        public  readonly int $chunkIndex)
    {
    }
}
