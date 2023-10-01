<?php

namespace App\Services\FileService;

use App\DTO\StoreFileDTO;

interface FileServiceInterface
{
    public function store(StoreFileDTO $storeFileDTO);
    public function get();

}
