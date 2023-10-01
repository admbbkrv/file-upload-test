<?php

namespace App\DTO;

class StoreFileFromChunksDTO
{
    public function __construct(
        public  readonly string $pathChunksDirectory,
        public  readonly int $totalChunks,
        )
    {
    }
}
