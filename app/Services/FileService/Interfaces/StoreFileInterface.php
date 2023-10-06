<?php

namespace App\Services\FileService\Interfaces;

use App\Services\FileService\DTO\StoreFileDTO;

interface StoreFileInterface
{
    public function storeFile(StoreFileDTO $dto): string;
}
